import { Client } from "@elastic/elasticsearch";
import fs from 'fs';
import moment from 'moment';

// Initialize Elasticsearch Client
const client = new Client({
  node: "http://elastic-tdv.northeurope.cloudapp.azure.com:9200", // Replace with your actual Elasticsearch endpoint
});

// Function to check if index exists
async function checkIndexExists(indexName) {
  try {
    const { body } = await client.indices.exists({ index: indexName });
    return body;
  } catch (error) {
    console.error(`Error checking index existence: ${error.message}`);
    throw error;
  }
}

// Function to create an index
async function createIndex(indexName, mapping = {}) {
  try {
    const indexExists = await checkIndexExists(indexName);
    if (!indexExists) {
      await client.indices.create({
        index: indexName,
        body: mapping,
      });
      console.log(`Index "${indexName}" created successfully`);
    } else {
      console.log(`Index "${indexName}" already exists.`);
    }
  } catch (error) {
    console.error(`Error creating index: ${error.message}`);
  }
}

// Function to add a single document
async function addDocument(indexName, id, document) {
  try {
    await client.index({
      index: indexName,
      id,
      body: document,
    });
    console.log(`Document added with ID "${id}"`);
  } catch (error) {
    console.error(`Error adding document: ${error.message}`);
  }
}

// Function to bulk index documents
async function bulkAddDocuments(indexName, documents) {
  try {
    const bulkOperations = [];
    for (const { id, body } of documents) {
      if (body.post_date) {
        body.post_date = moment(body.post_date).toISOString(); 
        body.post_modified = moment(body.post_modified).toISOString();
      }
      bulkOperations.push({ index: { _index: indexName, _id: id } });
      bulkOperations.push(body);
    }
    
    console.log("🚀 ~ bulkAddDocuments ~ bulkOperations:", bulkOperations)
    const res = await client.bulk({ body: bulkOperations });
    if (res.errors) {
      res.items.forEach((item, index) => {
        if (item.index && item.index.error) {
          console.error(
            `Error indexing document ${index}:`,
            item.index.error.reason
          );
        }
      });
    } else {
      console.log("Bulk operation successful");
    }
  } catch (error) {
    console.error(`Error in bulk operation: ${error.message}`);
  }
}

// Usage
(async function main() {
  const indexName = "wp_posts";
  
  // Define your mapping based on the fields of the WordPress post data
  const mapping = {
    mappings: {
      properties: {
        ID: { type: "integer" },
        post_author: { type: "integer" },
        post_date: { type: "date" },
        post_content: { type: "text" },
        post_title: { type: "text" },
        post_excerpt: { type: "text" },
        post_status: { type: "keyword" },
        comment_status: { type: "keyword" },
        ping_status: { type: "keyword" },
        post_password: { type: "keyword" },
        post_name: { type: "keyword" },
        post_modified: { type: "date" },
        guid: { type: "keyword" },
        post_type: { type: "keyword" },
        comment_count: { type: "integer" },
      },
    },
  };

  // Create index
  await createIndex(indexName, mapping);

  // Load JSON data from file (replace with your JSON file path)
  const rawData = fs.readFileSync('wp_posts.json');
  const posts = JSON.parse(rawData);

  // Prepare documents for bulk indexing
  const documents = posts.map((post) => ({
    id: post.ID.toString(),  // Ensure ID is treated as a string
    body: {
      ID: post.ID,
      post_author: post.post_author,
      post_date: post.post_date,
      post_content: post.post_content,
      post_title: post.post_title,
      post_excerpt: post.post_excerpt,
      post_status: post.post_status,
      comment_status: post.comment_status,
      ping_status: post.ping_status,
      post_password: post.post_password,
      post_name: post.post_name,
      post_modified: post.post_modified,
      guid: post.guid,
      post_type: post.post_type,
      comment_count: post.comment_count,
    },
  }));

  // Bulk add documents to Elasticsearch
  await bulkAddDocuments(indexName, documents);
})();

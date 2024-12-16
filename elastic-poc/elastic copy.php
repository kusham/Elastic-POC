<!-- <?php 

// require 'vendor/autoload.php';

// use Elastic\Elasticsearch\ClientBuilder;

// // Initialize Elasticsearch Client
// $client = ClientBuilder::create()
//     ->setHosts(['http://elastic-tdv.northeurope.cloudapp.azure.com:9200']) // Replace with your Elasticsearch endpoint
//     ->build();

// function checkIndexExists($client, $indexName) {
//     try {
//         return $client->indices()->exists(['index' => $indexName]);
//     } catch (Exception $e) {
//         echo "Error checking index existence: " . $e->getMessage() . PHP_EOL;
//         throw $e;
//     }
// }

// function createIndex($client, $indexName, $mapping = []) {
//     try {
//         if (!checkIndexExists($client, $indexName)) {
//             $params = [
//                 'index' => $indexName,
//                 'body'  => $mapping,
//             ];
//             $client->indices()->create($params);
//             echo "Index \"$indexName\" created successfully." . PHP_EOL;
//         } else {
//             echo "Index \"$indexName\" already exists." . PHP_EOL;
//         }
//     } catch (Exception $e) {
//         echo "Error creating index: " . $e->getMessage() . PHP_EOL;
//     }
// }

// function addDocument($client, $indexName, $id, $document) {
//     try {
//         $params = [
//             'index' => $indexName,
//             'id'    => $id,
//             'body'  => $document,
//         ];
//         $client->index($params);
//         echo "Document added with ID \"$id\"." . PHP_EOL;
//     } catch (Exception $e) {
//         echo "Error adding document: " . $e->getMessage() . PHP_EOL;
//     }
// }

// function bulkAddDocuments($client, $indexName, $documents) {
//     try {
//         $bulkParams = ['body' => []];

//         foreach ($documents as $doc) {
//             $bulkParams['body'][] = [
//                 'index' => [
//                     '_index' => $indexName,
//                     '_id'    => $doc['id'],
//                 ],
//             ];
//             $bulkParams['body'][] = $doc['body'];
//         }

//         $client->bulk($bulkParams);
//         echo "Bulk operation successful." . PHP_EOL;
//     } catch (Exception $e) {
//         echo "Error in bulk operation: " . $e->getMessage() . PHP_EOL;
//     }
// }


// Usage
// $indexName = 'titles';
// $mapping = [
//     'mappings' => [
//         'properties' => [
//             'title' => ['type' => 'text'],
//             'author' => ['type' => 'keyword'],
//             'date' => ['type' => 'date'],
//         ],
//     ],
// ];

// Create index
// createIndex($client, $indexName, $mapping);

// // Add single document
// addDocument($client, $indexName, '1', [
//     'title' => 'My First Title',
//     'author' => 'Jaseem',
//     'date' => date('c'), 
// ]);

// // Add multiple documents in bulk
// $documents = [];
// for ($i = 2; $i <= 11; $i++) {
//     $documents[] = [
//         'id' => $i,
//         'body' => [
//             'title' => "Bulk Title $i",
//             'author' => 'Author',
//             'date' => date('c'),
//         ],
//     ];
// }

// bulkAddDocuments($client, $indexName, $documents);

// ?> -->


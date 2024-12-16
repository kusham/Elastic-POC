<?php 
/**
 * Plugin Name: Elasticsearch Sync
 * Description: Sync WordPress posts with Elasticsearch.
 * Version: 1.1
 * Author: Your Name
 */

require 'vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;

function es_get_client() {
    return ClientBuilder::create()
        ->setHosts(['http://elastic-tdv.northeurope.cloudapp.azure.com:9200']) // Replace with your Elasticsearch endpoint
        ->build();
}

function es_create_index() {
    $client = es_get_client();
    $params = [
        'index' => 'wordpress-posts',
        'body' => [
            'mappings' => [
                'properties' => [
                    'ID'              => ['type' => 'integer'],
                    'post_author'     => ['type' => 'integer'],
                    'post_date'       => ['type' => 'date'],
                    'post_content'    => ['type' => 'text'],
                    'post_title'      => ['type' => 'text'],
                    'post_excerpt'    => ['type' => 'text'],
                    'post_status'     => ['type' => 'keyword'],
                    'comment_status'  => ['type' => 'keyword'],
                    'ping_status'     => ['type' => 'keyword'],
                    'post_password'   => ['type' => 'keyword'],
                    'post_name'       => ['type' => 'keyword'],
                    'post_modified'   => ['type' => 'date'],
                    'guid'            => ['type' => 'keyword'],
                    'post_type'       => ['type' => 'keyword'],
                    'comment_count'   => ['type' => 'integer'],
                ],
            ],
        ],
    ];

    // Check if the index already exists before creating
    if (!$client->indices()->exists(['index' => 'wordpress-posts'])->asBool()) {
        $client->indices()->create($params);
    }
}

function es_index_post($post_id) {
    $post = get_post($post_id);

    // Only index published posts
    if ($post->post_status !== 'publish') {
        return;
    }

    $client = es_get_client();

    $post_date = (new DateTime($post->post_date))->format(DateTime::ATOM);
    $post_modified = (new DateTime($post->post_modified))->format(DateTime::ATOM);

    $params = [
        'index' => 'wordpress-posts',
        'id'    => $post_id,
        'body'  => [
            'ID'             => $post->ID,
            'post_author'    => $post->post_author,
            'post_date'      => $post_date,
            'post_content'   => $post->post_content,
            'post_title'     => $post->post_title,
            'post_excerpt'   => $post->post_excerpt,
            'post_status'    => $post->post_status,
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_password'  => $post->post_password,
            'post_name'      => $post->post_name,
            'post_modified'  => $post_modified,
            'guid'           => $post->guid,
            'post_type'      => $post->post_type,
            'comment_count'  => $post->comment_count,
        ],
    ];

    $client->index($params);
}

function es_delete_post($post_id) {
    $client = es_get_client();
    $params = [
        'index' => 'wordpress-posts',
        'id'    => $post_id,
    ];

    // Safely attempt to delete the document
    if ($client->exists($params)->asBool()) {
        $client->delete($params);
    }
}

// Hooks for handling publishing, updating, and deleting
add_action('publish_post', 'es_index_post'); // Hook for publishing posts
add_action('edit_post', 'es_index_post');    // Hook for updating posts
add_action('delete_post', 'es_delete_post'); // Hook for deleting posts

// Ensure the Elasticsearch index exists on plugin activation
register_activation_hook(__FILE__, 'es_create_index');

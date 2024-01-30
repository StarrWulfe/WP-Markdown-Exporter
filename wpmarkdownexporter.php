<?php
/**
 * Plugin Name: WP-Markdown-Exporter
 * Description: Plugin to enable a method to export WordPress posts to Markdown files with front matter YAML.
 * Version: 1.1
 * Author: J.L. Gatewood
 */

// Add a menu item under Tools
function add_markdown_exporter_menu() {
    add_submenu_page(
        'tools.php',
        'Markdown Exporter',
        'Markdown Exporter',
        'manage_options',
        'markdown-exporter',
        'markdown_exporter_page'
    );
}
add_action('admin_menu', 'add_markdown_exporter_menu');

// Export posts to Markdown with front matter YAML
function markdown_exporter_page() {
    // Check if the user has the capability to export
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Get all posts
    $posts = get_posts(array(
        'numberposts' => -1,
    ));

    // Loop through posts and export to Markdown
    foreach ($posts as $post) {
        $filename = sanitize_title($post->post_title) . '.md';

        // Prepare front matter YAML
        $front_matter = "---\n";
        $front_matter .= "title: \"" . $post->post_title . "\"\n";
        $front_matter .= "date: " . $post->post_date . "\n";
        $front_matter .= "url: " . get_permalink($post->ID) . "\n";
        $front_matter .= "tags: " . implode(', ', wp_get_post_tags($post->ID, array('fields' => 'names'))) . "\n";
        $front_matter .= "categories: " . implode(', ', wp_get_post_categories($post->ID, array('fields' => 'names'))) . "\n";
        $front_matter .= "---\n\n";

        // Append content to front matter
        $markdown_content = $front_matter . $post->post_content;

        // Save Markdown content to a file
        file_put_contents(ABSPATH . $filename, $markdown_content);
    }

    // Display a success message
    echo '<div class="updated"><p>Posts exported to Markdown successfully!</p></div>';
}

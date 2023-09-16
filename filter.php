<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
$args = array(
    'post_type' => 'job',
    'posts_per_page' => -1,
);
if (!empty($_GET['job_type'])) {
    $args['tax_query'][] = array(
        'taxonomy' => 'job_type',
        'field' => 'slug',
        'terms' => $_GET['job_type'],
    );
}

if (!empty($_GET['industry'])) {
    $args['tax_query'][] = array(
        'taxonomy' => 'industry',
        'field' => 'slug',
        'terms' => $_GET['industry'],
    );
}

if (!empty($_GET['skills'])) {
    $args['tax_query'][] = array(
        'taxonomy' => 'skills',
        'field' => 'slug',
        'terms' => $_GET['skills'],
    );
}

$query = new WP_Query($args);
$response = "";
if ($query->have_posts()) :
 
    $response = "<ul id='job_list'>";
    while ($query->have_posts()) : $query->the_post();
    $response.= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
    endwhile;
    $response.= '</ul>';
    
else :
    $response = "No jobs found.";
endif;
echo $response;
//wp_reset_postdata();
return $response;
return ob_get_clean();
?>
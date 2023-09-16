<?php
/**
 * Plugin Name: Job Seeker
 * Description: A Plugin to manage Job Listing.
 * Version: 0.1
 * Author: Payal Shihora
 **/
// Register custom post type for jobs
ob_start();
?>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>

<?php
function register_job_post_type()
{
    $labels = array(
        'name' => 'Jobs',
        'singular_name' => 'Job',
        'add_new' => 'Add New Job',
        'add_new_item' => 'Add New Job',
        'edit_item' => 'Edit Job',
        'new_item' => 'New Job',
        'view_item' => 'View Job',
        'search_items' => 'Search Jobs',
        'not_found' => 'No jobs found',
        'not_found_in_trash' => 'No jobs found in Trash',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'menu_position' => 5,
        'supports' => array('title', 'editor', 'custom-fields'),
    );

    register_post_type('job', $args);
}
add_action('init', 'register_job_post_type');

// Register custom taxonomies for job type and industry
function register_job_taxonomies()
{
    // Job Type
    register_taxonomy('job_type', 'job', array(
        'label' => 'Job Type',
        'hierarchical' => true,
        'public' => true,
    ));

    // Industry
    register_taxonomy('industry', 'job', array(
        'label' => 'Industry',
        'hierarchical' => true,
        'public' => true,
    ));
}
add_action('init', 'register_job_taxonomies');

// Register custom tags for skills
function register_job_tags()
{
    register_taxonomy('skills', 'job', array(
        'label' => 'Skills',
        'hierarchical' => false,
        'public' => true,
    ));
}
add_action('init', 'register_job_tags');

function job_listing_shortcode()
{
    ob_start();
?>

    <div>
        <label for="job_type_filter">Job Type:</label>
        <select id="job_type_filter">
            <option value="">All</option>
            <?php
            $terms = get_terms(array('taxonomy' => 'job_type'));
            foreach ($terms as $term) {
                echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            ?>
        </select>
    </div>

    <div>
        <label for="industry_filter">Industry:</label>
        <select id="industry_filter">
            <option value="">All</option>
            <?php
            $terms = get_terms(array('taxonomy' => 'industry'));
            foreach ($terms as $term) {
                echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            ?>
        </select>
    </div>

    <div>
        <label for="skills_filter">Skills:</label>
        <select id="skills_filter">
            <option value="">All</option>
            <?php
            $terms = get_terms(array('taxonomy' => 'skills'));
            foreach ($terms as $term) {
                echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            ?>
        </select>
    </div>

    <div><button id="filter_jobs">Filter Jobs</button></div>
<?php

    $args = array(
        'post_type' => 'job',
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);
    //show job listing with all jobs
    if ($query->have_posts()) :
        echo "<ul id='job_list'>";
        while ($query->have_posts()) : $query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        endwhile;
        echo '</ul>';
    else :
        echo 'No jobs found.';
    endif;
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('job_listing', 'job_listing_shortcode');
?>

<script>
    var plugin_ajax_url = '<?php echo plugins_url(); ?>/JobSeeker/filter.php';
    jQuery(document).ready(function($) {
        $('#filter_jobs').click(function() {
            var job_type1 = $("#job_type_filter").val();
            var industry1 = $("#industry_filter").val();
            var skills1 = $("#skills_filter").val();
            $.ajax({
                type: 'GET',
                url: plugin_ajax_url,
                data: {
                    job_type: job_type1,
                    industry: industry1,
                    skills: skills1,
                },
                success: function(data) {
                    $('#job_list').html(data);
                }
            });
        });
    });
</script>
<?php ob_flush();?>
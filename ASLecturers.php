<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Displays a list of Avengerschool lecturers.
 *
 * @author Lee Han Kyeol
 * @version 0.0.1
 */
class ASLecturers
{
    private $AS_DISPLAY_NAME = '어벤져스쿨';
    private $POST_TYPE = 'product';
    private $REP_LECTURER_FIELD_ID = 'rep_lecture_id';
    private $LECTURER_DESC_FIELD_ID = 'lecturer_description';
    private $TAXONOMY = 'product_cat';
    private $PER_PAGE = 30;
    private $HALF_NUM_PAGES_TO_DISPLAY = 2;

    private $PARAM_PAGE = 'lecturer_page';
    private $PARAM_CATEGORY = 'lecture_category';

    private $ALLOWED_USERMETA = array('description', 'facebook', 'twitter', 'lecturer_description');

    public function __construct()
    {
        // Add rep_lecture_id, lecturer_description user meta.
        // http://justintadlock.com/archives/2009/09/10/adding-and-using-custom-user-profile-fields
        add_action('show_user_profile', array($this, 'add_as_custom_fields'));
        add_action('edit_user_profile', array($this, 'add_as_custom_fields'));
        add_action('personal_options_update', array($this, 'save_as_custom_fields'));
        add_action('edit_user_profile_update', array($this, 'save_as_custom_fields'));

        // Add a shortcode.
        add_shortcode('display_as_lecturers', array($this, 'display_as_lecturers'));
    }

    public function add_as_custom_fields($user)
    {
        // Rep. lecture
        $args = array(
            'author' => $user->ID,
            'post_type' => $this->POST_TYPE
        );
        $lectures = get_posts($args);
        $rep_lecture_id = get_user_meta($user->ID, $this->REP_LECTURER_FIELD_ID, TRUE);

        echo '<h3>대표 강연</h3>
              <table class="form-table">
                <tr>
			        <th><label for="' . $this->REP_LECTURER_FIELD_ID . '">대표강연</label></th>
                    <td>';

        echo '<select id="' . $this->REP_LECTURER_FIELD_ID . '" name="' . $this->REP_LECTURER_FIELD_ID . '">';
        foreach ($lectures as $lecture) {
            if ($rep_lecture_id && $lecture->ID == $rep_lecture_id) {
                echo '<option value="' . $lecture->ID . '" selected="selected">' . get_the_title($lecture->ID) . '</option>';
            } else {
                echo '<option value="' . $lecture->ID . '">' . get_the_title($lecture->ID) . '</option>';
            }
        }
        echo '</select>';
        echo '<span class="description">대표 강연을 선택해주세요.</span>
		        	</td>
		        </tr>
	          </table>';

        // Lecturer description
        echo '<h3>강연자 설명</h3>
                <table class="form-table">
                    <tr>
                        <th><label for="' . $this->LECTURER_DESC_FIELD_ID . '">강연자 설명</label></th>
                        <td>';
        echo '<textarea id="' . $this->LECTURER_DESC_FIELD_ID . '" name="' . $this->LECTURER_DESC_FIELD_ID . '" rows="5" cols="30">';
        echo get_user_meta($user->ID, $this->LECTURER_DESC_FIELD_ID, true);
        echo '</textarea>';
        echo '<span class="description">강연자 목록에 노출될 설명을 입력해주세요.</span>
                        </td>
                    </tr>
                </table>';
    }

    public function save_as_custom_fields($user_id)
    {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        $meta[$this->REP_LECTURER_FIELD_ID] = $_POST[$this->REP_LECTURER_FIELD_ID];
        $meta[$this->LECTURER_DESC_FIELD_ID] = $_POST[$this->LECTURER_DESC_FIELD_ID];

        foreach ($meta as $key => $value) {
            if (get_user_meta($user_id, $key, TRUE)) {
                update_user_meta($user_id, $key, $value);
            } else {
                add_user_meta($user_id, $key, $value);
            }
            if (!$value) {
                delete_user_meta($user_id, $key);
            }
        }
    }

    public function display_as_lecturers()
    {
        global $wpdb;

        $category_slug = $_GET[$this->PARAM_CATEGORY];
        $page = $_GET[$this->PARAM_PAGE];

        if ($page == null) {
            $page = 1;
        }

        $users = $wpdb->get_results($this->getLecturersQuery($category_slug, $page));

        // Avoid n+1 queries.
        $user_ids_string = '(';
        $rep_lecture_ids_string = '(';
        foreach ($users as $user) {
            $user_ids_string .= $user->user_id . ',';
            $rep_lecture_ids_string .= $user->rep_lecture_id . ',';
        }
        if (strlen($user_ids_string) > 1) {
            $user_ids_string = substr($user_ids_string, 0, strlen($user_ids_string) - 1);
        }
        if (strlen($rep_lecture_ids_string) > 1) {
            $rep_lecture_ids_string = substr($rep_lecture_ids_string, 0, strlen($rep_lecture_ids_string) - 1);
        }
        $user_ids_string .= ')';
        $rep_lecture_ids_string .= ')';

        $users_metadata = $wpdb->get_results(
            'SELECT *
                FROM ' . $wpdb->usermeta . ' AS um
                WHERE um.user_id IN ' . $user_ids_string
        );
        $lectures = $wpdb->get_results(
            'SELECT ID, post_author, post_title, post_name
                FROM ' . $wpdb->posts . ' AS p
                WHERE p.ID IN ' . $rep_lecture_ids_string
        );

        foreach ($users_metadata as $user_metadata) {
            $key = $user_metadata->meta_key;
            $value = $user_metadata->meta_value;
            if (in_array($key, $this->ALLOWED_USERMETA)) {
                foreach ($users as $user) {
                    if ($user->user_id === $user_metadata->user_id) {
                        $user->$key = $value;
                        break;
                    }
                }
            }
        }
        foreach ($lectures as $lecture) {
            foreach ($users as $user) {
                if ($lecture->post_author == $user->user_id) {
                    $user->rep_lecture = $lecture;
                }
            }
        }

        // Get product category
        $terms = get_terms(array(
            'taxonomy' => $this->TAXONOMY
        ));

        $this->printTabs($terms);
        $this->printLecturers($users);

        // Pagination.
        $lecturers_count = count($wpdb->get_results($this->getLecturersQuery($category_slug, null)));
        $num_pages = ceil(((float)$lecturers_count / (float)$this->PER_PAGE));
        if ($category_slug == null) {
            $link = get_permalink() . '?';
        } else {
            $link = get_permalink() . '?' . $this->PARAM_CATEGORY . '=' . $category_slug . '&';
        }

        echo '<div class="jogger">';

        $min_page = max(1, $page - $this->HALF_NUM_PAGES_TO_DISPLAY);
        $max_page = min($num_pages, $page + $this->HALF_NUM_PAGES_TO_DISPLAY);

        if ($page != 1) {
            echo '<a href="' . $this->getPageLink($link, ($page - 1)) . '">« Previous Page</a>' . "\n";
        }
        if ($min_page > 1) {
            echo '<a href="' . $this->getPageLink($link, 1) . '">1</a>' . "\n";
        }
        if ($min_page > 2) {
            echo '…' . "\n";
        }
        for ($i = $min_page; $i < $max_page + 1; $i++) {
            $echo = '<a href=" ' . $this->getPageLink($link, $i) . '"';
            if ($i == $page) {
                $echo .= ' class="current" aria-label="Current page"';
            }
            $echo .= '>' . $i . '</a>' . "\n";
            echo $echo;
        }

        if ($max_page < $num_pages - 1) {
            echo '…' . "\n";
        }
        if ($max_page < $num_pages) {
            echo '<a href="' . $this->getPageLink($link, $num_pages) . '">' . $num_pages . '</a>' . "\n";
        }
        if ($page != $num_pages) {
            echo '<a href="' . $this->getPageLink($link, ($page + 1)) . '">Next Page »</a>' . "\n";
        }

        echo '</div>';
    }

    /**
     * Prints product (lecture) categories.
     *
     * @param $terms
     */
    private function printTabs($terms)
    {
        echo '<ul class="as-lecturers-ul">';

        echo '<li class="as-lecturers-li"><span class="bold">카테고리</span></li>';

        echo '<li class="as-lecturers-li"><a href="' . get_permalink() . '">전체</a></li>';
        foreach ($terms as $term) {
            if ($term->slug == 'package') {
                continue;
            }
            echo '<li class="as-lecturers-li">';
            echo '<a href=" ' . get_permalink() . '?' . $this->PARAM_CATEGORY . '=' . $term->slug . '">' . $term->name . '</a>';
            echo '</li>';
        }

        echo '</ul>';
    }

    /**
     * Prints lecturers.
     *
     * @param $users
     */
    private function printLecturers($users)
    {
        $lecturer_description_id = $this->LECTURER_DESC_FIELD_ID;

        foreach ($users as $user) {
            // TODO: Update design.
            // For clear codes...
            if ($user->blog) {
                $user->blog = '<li><a class="cmsmasters-icon-custom-blogger-1" target="_blank" href="' . $user->blog . '"></a></li>' . "\n";
            } else {
                $user->blog = '';
            }
            if ($user->facebook) {
                $user->facebook = '<li><a class="cmsmasters-icon-custom-facebook-6" target="_blank" href="' . $user->facebook . '"></a></li>' . "\n";
            } else {
                $user->facebook = '';
            }
            // TODO: Gotta find the Twitter icon class...
            if ($user->twitter) {
                $user->twitter = '<li><a class="author-box-icon" target="_blank" href="' . $user->twitter . '"></a></li>' . "\n";
            } else {
                $user->twitter = '';
            }

            echo '<div class="cmsmasters_profile vertical">';
            echo '<article class="profile shortcode_animated">';

            // Profile image
            echo '<div class="pl_img">';
            echo '<figure>';
            echo get_wp_user_avatar($user->user_id, 'medium');
            echo '</figure>';
            echo '</div>';

            // Profile content
            echo '<div class="pl_content">';

            echo '<h2 class="entry-title">';
            echo '<a>강연자: <span class="bold">' . $user->display_name . '</span></a>';
            echo '<div class="pl_social">';
            echo '<ul class="pl_social_list">' . "\n";
            echo $user->blog;
            echo $user->facebook;
            echo $user->twitter;
            echo '</ul>';
            echo '</div>';
            echo '</h2>';

            echo '<div class="entry-content">';
            echo $user->$lecturer_description_id;
            echo '</div>';

            echo '<div class="entry-content">';
            echo '<span class="bold">대표강연</span>: ' . '<a href=" ' . get_permalink($user->rep_lecture->ID) . ' ">' . $user->rep_lecture->post_title . '</a>';
            echo '</div>';

            echo '</div>';

            echo '</article>';
            echo '</div>';
        }
    }

    /**
     * Returns query for lecturers.
     *
     * @param $category_slug
     * @param $page
     * @return string
     */
    private function getLecturersQuery($category_slug, $page)
    {
        global $wpdb;

        if ($category_slug) {
            $join = 'LEFT JOIN ' . $wpdb->term_relationships . ' AS tr ON p.ID = tr.object_id
                    LEFT JOIN ' . $wpdb->term_taxonomy . ' AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    LEFT JOIN ' . $wpdb->terms . ' AS t ON tt.term_id = t.term_id
                  WHERE p.post_status = \'publish\'
                    AND tt.taxonomy = \'' . $this->TAXONOMY . '\'
                    AND t.slug = \'' . $category_slug . '\'
                    AND u.display_name != \'' . $this->AS_DISPLAY_NAME . '\'
                    AND um.meta_key = \'' . $this->REP_LECTURER_FIELD_ID . '\'';
        } else {
            $join = 'WHERE p.post_status = \'publish\'
                AND u.display_name != \'' . $this->AS_DISPLAY_NAME . '\'
                    AND um.meta_key = \'' . $this->REP_LECTURER_FIELD_ID . '\'';
        }

        if ($page) {
            $innerSelect = 'u.user_url AS blog, u.display_name, um.meta_value AS rep_lecture_id, p.post_title, p.post_name,';
            $limit = 'LIMIT ' . $this->PER_PAGE . '
            OFFSET ' . (intval($page) - 1) * intval($this->PER_PAGE);
        } else {
            $innerSelect = 'u.display_name,';
            $limit = '';
        }

        return 'SELECT TEMP.* 
                FROM (
                  SELECT u.ID AS user_id, ' . $innerSelect . ' p.post_date_gmt AS post_date
                  FROM ' . $wpdb->users . ' AS u
                    LEFT JOIN ' . $wpdb->usermeta . ' AS um ON u.ID = um.user_id
                    INNER JOIN ' . $wpdb->posts . ' AS p ON p.ID = um.meta_value ' . $join . '
                ) AS TEMP
            GROUP BY user_id
            ORDER BY display_name ASC ' . $limit;
    }

    /**
     * Returns page link.
     *
     * @param $link
     * @param $page
     * @return string
     */
    private function getPageLink($link, $page)
    {
        $result = $link . ($page == 1 ? '' : ($this->PARAM_PAGE . '=' . $page));
        if ($result[strlen($result) - 1] == '&') {
            $result = substr($result, 0, strlen($result) - 1);
        }
        if ($result[strlen($result) - 1] == '?') {
            $result = substr($result, 0, strlen($result) - 1);
        }
        return $result;
    }
}

$as_lecturers = new ASLecturers();

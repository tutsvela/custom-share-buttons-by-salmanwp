// SalmanWP Custom Share Button for KadenceWP

// Function to add share button in Posts - Pages
function kadencewp_add_share_buttons_meta_box() {
    add_meta_box(
        'kadencewp_share_buttons_meta',
        'Share Buttons',
        'kadencewp_share_buttons_meta_callback',
        ['post', 'page', 'custom_post_type'], // Add support for custom post types here
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'kadencewp_add_share_buttons_meta_box');

// Callback for the meta box
function kadencewp_share_buttons_meta_callback($post) {
    // Retrieve the current meta values
    $disable_share = get_post_meta($post->ID, '_disable_share_buttons', true);
    $disable_dynamic_text = get_post_meta($post->ID, '_disable_dynamic_text', true);
    $custom_text = get_post_meta($post->ID, '_custom_share_text', true);
    wp_nonce_field('kadencewp_save_share_buttons_meta', 'kadencewp_share_buttons_nonce');
    ?>
    <p>
        <label for="kadencewp_disable_share_buttons">
            <input type="checkbox" id="kadencewp_disable_share_buttons" name="kadencewp_disable_share_buttons" value="1" <?php checked($disable_share, '1'); ?> />
            Disable Share Buttons
        </label>
    </p>
    <p>
        <label for="kadencewp_disable_dynamic_text">
            <input type="checkbox" id="kadencewp_disable_dynamic_text" name="kadencewp_disable_dynamic_text" value="1" <?php checked($disable_dynamic_text, '1'); ?> />
            Disable Dynamic Text
        </label>
    </p>
    <p id="custom-text-field" style="<?php echo $disable_dynamic_text === '1' ? '' : 'display: none;'; ?>">
        <label for="kadencewp_custom_share_text">Custom Text:</label>
        <input type="text" id="kadencewp_custom_share_text" name="kadencewp_custom_share_text" value="<?php echo esc_attr($custom_text); ?>" style="width: 100%;" />
    </p>
    <p>
        <a href="https://salmanwp.com" target="_blank" style="text-decoration: none; font-weight: bold; color: #0073aa;">
            Need help? Get Support!
        </a>
    </p>
    <script>
        document.getElementById('kadencewp_disable_dynamic_text').addEventListener('change', function() {
            document.getElementById('custom-text-field').style.display = this.checked ? '' : 'none';
        });
    </script>
    <?php
}

// Save the meta box values
function kadencewp_save_share_buttons_meta($post_id) {
    if (!isset($_POST['kadencewp_share_buttons_nonce']) || !wp_verify_nonce($_POST['kadencewp_share_buttons_nonce'], 'kadencewp_save_share_buttons_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['kadencewp_disable_share_buttons'])) {
        update_post_meta($post_id, '_disable_share_buttons', '1');
    } else {
        delete_post_meta($post_id, '_disable_share_buttons');
    }

    if (isset($_POST['kadencewp_disable_dynamic_text'])) {
        update_post_meta($post_id, '_disable_dynamic_text', '1');
        $custom_text = sanitize_text_field($_POST['kadencewp_custom_share_text']);
        update_post_meta($post_id, '_custom_share_text', $custom_text);
    } else {
        delete_post_meta($post_id, '_disable_dynamic_text');
        delete_post_meta($post_id, '_custom_share_text');
    }
}
add_action('save_post', 'kadencewp_save_share_buttons_meta');

// Add share buttons below the content for posts, pages, and custom post types
function kadencewp_custom_share_buttons($content) {
    if ((is_single() || is_page() || is_singular()) && in_the_loop()) {
        global $post;

        // Check if share buttons are disabled for the current post/page
        $disable_share = get_post_meta($post->ID, '_disable_share_buttons', true);
        if ($disable_share === '1') {
            return $content; // Don't show buttons if disabled
        }

        // Check if dynamic text is disabled
        $disable_dynamic_text = get_post_meta($post->ID, '_disable_dynamic_text', true);
        $custom_text = get_post_meta($post->ID, '_custom_share_text', true);

        // Static text
        $static_message = "Sharing is Caring!";
        
        // Determine which text to use
        $message = ($disable_dynamic_text === '1' && !empty($custom_text)) ? $custom_text : $static_message;

        // Social share buttons HTML
        $share_buttons = '
        <div class="kadence-share-section" style="text-align: center; margin-top: 20px;">
            <span class="animated-heading">' . $message . '</span>
            <div class="kadence-share-buttons" style="display: inline-block; margin-left: 10px;">
                <a href="https://www.facebook.com/sharer/sharer.php?u=' . get_permalink() . '" class="share-btn fb-btn" target="_blank">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a href="https://twitter.com/share?url=' . get_permalink() . '&text=' . get_the_title() . '" class="share-btn x-btn" target="_blank">
                    <i class="fab fa-x-twitter"></i> Twitter
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=' . get_permalink() . '" class="share-btn linkedin-btn" target="_blank">
                    <i class="fab fa-linkedin"></i> LinkedIn
                </a>
                <a href="https://api.whatsapp.com/send?text=' . get_the_title() . ' ' . get_permalink() . '" class="share-btn whatsapp-btn" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="https://pinterest.com/pin/create/button/?url=' . get_permalink() . '&description=' . get_the_title() . '" class="share-btn pinterest-btn" target="_blank">
                    <i class="fab fa-pinterest"></i> Pinterest
                </a>
            </div>
        </div>';

        // Append share buttons to the content
        return $content . $share_buttons;
    }

    return $content;
}
add_filter('the_content', 'kadencewp_custom_share_buttons');

// Enqueue Font Awesome for social icons
function kadencewp_enqueue_fontawesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'kadencewp_enqueue_fontawesome');

// Add custom styles for animated heading and button hover effects
function kadencewp_custom_share_styles() {
    echo '
    <style>
        .kadence-share-section {
            margin-top: 30px;
            text-align: center;
        }

        .animated-heading {
            font-size: 18px;
            font-family: "Arial", sans-serif;
            color: #333;
            font-weight: bold;
            vertical-align: middle;
            animation: bounceIn 2s infinite;
            display: inline-block;
        }

        /* Keyframe for text animation */
        @keyframes bounceIn {
            0% { transform: scale(1) translateY(0); }
            30% { transform: scale(1.1) translateY(-5px); }
            50% { transform: scale(1.2) translateY(-10px); }
            100% { transform: scale(1) translateY(0); }
        }

        /* Button Styles */
        .kadence-share-buttons a {
            margin: 0 10px;
            font-size: 14px;
            display: inline-block;
            color: #fff;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
        }

        /* Brand colors */
        .fb-btn { background-color: #1877f2; }
        .x-btn { background-color: #000; }
        .linkedin-btn { background-color: #0077b5; }
        .whatsapp-btn { background-color: #25d366; }
        .pinterest-btn { background-color: #e60023; }

        /* Button Hover Effects - Move up and shadow */
        .kadence-share-buttons a:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        /* Responsive Styles for smaller devices */
                @media only screen and (max-width: 1024px) {
            .kadence-share-section {
                margin-top: 20px;
            }

            .kadence-share-buttons a {
                font-size: 13px;
                padding: 8px 16px;
                margin: 5px;
            }

            .animated-heading {
                font-size: 16px;
            }
        }

        @media only screen and (max-width: 768px) {
            .kadence-share-buttons {
                display: block;
                margin: 0 auto;
            }

            .kadence-share-buttons a {
                display: block;
                width: 100%;
                margin: 10px 0;
                text-align: center;
            }

            .animated-heading {
                font-size: 16px;
            }
        }

        @media only screen and (max-width: 480px) {
            .kadence-share-buttons a {
                font-size: 12px;
                padding: 6px 12px;
            }

            .animated-heading {
                font-size: 14px;
            }
        }
    </style>
    ';
}
add_action('wp_head', 'kadencewp_custom_share_styles');

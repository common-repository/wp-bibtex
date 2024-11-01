<?php
/**
 * Plugin Name: WP-BibTeX
 * Plugin URI: https://wordpress.org/plugins/wp-bibtex/
 * Description: A plugin helps format BibTeX entries to display a bibliography or cite citations in WordPress.
 * Author: Haozhe Xie
 * Author URI: https://haozhexie.com
 * Version: 3.0.1
 * License: GPL v2.0
 */
define('WP_BIBTEX_PLUGIN_PATH', plugin_dir_path(__FILE__));
require_once(WP_BIBTEX_PLUGIN_PATH . 'wp-bibtex-options.php');

/**
 * The require and optional fileds of BibTex entries.
 * Ref: 
 * - https://en.wikipedia.org/wiki/BibTeX
 * - https://verbosus.com/bibtex-style-examples.html
 * @var array
 */
$BIBTEX_ENTRIES = array(
    'article'           => array(
        'required'      => array('title', 'author', 'journal', 'year'),
        'optional'      => array('volume', 'number', 'pages', 'month', 'publisher', 'doi'),
        'bibliography'  => '{{author}}. {{title}}. <em>{{journal}}</em>[, {{volume}}][({{number}})][: {{pages}}][, {{month}}], {{year}}.[ {{note}}]',
    ),
    'book'              => array(
        'required'      => array('title', 'author', 'publisher', 'year'),
        'optional'      => array('volume', 'number', 'series', 'edition', 'month', 'address', 'isbn'),
        'bibliography'  => '{{author}}. <em>{{title}}</em>.[ Vol. {{volume}}.] {{publisher}}[, {{address}}][, {{month}}], {{year}}.[ {{note}}]',
    ),
    'inproceedings'       => array(
        'required'      => array('title', 'author', 'booktitle', 'year'),
        'optional'      => array('volume', 'number', 'series', 'pages', 'month', 'organization', 'publisher', 'address'),
        'bibliography'  => '{{author}}. {{title}}. <em>{{booktitle}}</em>[, Vol. {{volume}}][, pages {{pages}}][, {{address}}][, {{month}}], {{year}}.[ {{publisher}}.][ {{note}}]',
    ),
    'mastersthesis'     => array(
        'required'      => array('title', 'author', 'school', 'year'),
        'optional'      => array('month', 'address'),
        'bibliography'  => '{{author}}. {{title}}. Master\'s thesis, {{school}}[, {{address}}][, {{month}}], {{year}}.[ {{note}}]',
    ),
    'phdthesis'         => array(
        'required'      => array('title', 'author', 'school', 'year'),
        'optional'      => array('month', 'address'),
        'bibliography'  => '{{author}}. {{title}}. PhD thesis, {{school}}[, {{address}}][, {{month}}], {{year}}.[ {{note}}]',
    ),
    'unpublished'       => array(
        'required'      => array('title', 'author', 'year'),
        'optional'      => array('month'),
        'bibliography'  => '{{author}}. {{title}}.[ {{month}},] {{year}}.[ {{note}}]',
    ),
);

/**
 * Generate information for a citation.
 * @param  array  $attrs: an array contains attributes of the citation
 * @param  string $content: dummy parameter, will not used in the function
 * @return a formatted HTML contains expected information for the citation
 */
function wp_bibtex_shortcode($attrs, $content=null) {
    $missing_attributes = wp_bibtex_is_attrs_missing($attrs);
    if ( count($missing_attributes) != 0 ) {
        return '[ERROR] Missing attributes: '. implode(', ', $missing_attributes);
    }

    $citation_key       = wp_bibtex_get_citation_key($attrs);
    $bibliography_text  = wp_bibtex_get_bibliography_text($attrs);
    $bibtex_text        = wp_bibtex_get_bibtex_text($citation_key, $attrs);
    $additional_fields  = wp_bibtex_get_additional_fields($attrs);
    if ( array_key_exists("img", $attrs) ) {
        // Highlight style in v3.x
        return wp_bibtex_get_highlight_html($bibliography_text, $bibtex_text, $additional_fields,
                                            $attrs);
    } else {
        // Legacy style
        return wp_bibtex_get_legacy_html($bibliography_text, $bibtex_text, $additional_fields);
    }
    return $bibtex_content;
}

/**
 * Check whether there're missing attributes for the citation.
 * @param  array  $attrs: an array contains attributes of the citation
 * @return an array contains name of missing attributes
 */
function wp_bibtex_is_attrs_missing($attrs) {
    global $BIBTEX_ENTRIES;
    $citation_type      = $attrs['type'];
    $missing_attributes = array();

    if ( !array_key_exists($citation_type, $BIBTEX_ENTRIES) ) {
        array_push($missing_attributes, 'type');
    } else {
        $required_fields = $BIBTEX_ENTRIES[$citation_type]['required'];
        foreach ( $required_fields as $required_field ) {
            if ( !array_key_exists($required_field, $attrs) ) {
                array_push($missing_attributes, $required_field);
            }
        }
    }
    return $missing_attributes;
}

/**
 * Generate BibTeX citation key for the citation.
 * The citation key is a string like 'xie2016comparison'.
 * @param  array  $attrs: an array contains attributes of the citation
 * @return BibTeX citation key of the citation
 */
function wp_bibtex_get_citation_key($attrs) {
    $year               = $attrs['year'];
    $author             = $attrs['author'];
    $title              = $attrs['title'];
    $first_author       = explode('and', strtolower($author))[0];
    $last_name          = explode(',', $first_author)[0];
    $title_words        = wp_bibtex_multiexplode(array(' ', '-', ':'), strtolower($title));
    $stop_words         = array(
        "a", "about", "above", "after", "again", "against", "all", "am", "an", 
        "and", "any", "are", "aren't", "as", "at", "be", "because", "been", 
        "before", "being", "below", "between", "both", "but", "by", "can't", 
        "cannot", "could", "couldn't", "did", "didn't", "do", "does", "doesn't", 
        "doing", "don't", "down", "during", "each", "few", "for", "from", 
        "further", "had", "hadn't", "has", "hasn't", "have", "haven't", "having", 
        "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", 
        "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", 
        "if", "in", "into", "is", "isn't", "it", "it's", "its", "itself", "let's", 
        "me", "more", "most", "mustn't", "my", "myself", "no", "nor", "not", "of", 
        "off", "on", "once", "only", "or", "other", "ought", "our", "ours", 
        "ourselves", "out", "over", "own", "same", "shan't", "she", "she'd", 
        "she'll", "she's", "should", "shouldn't", "so", "some", "such", "than", 
        "that", "that's", "the", "their", "theirs", "them", "themselves", "then", 
        "there", "there's", "these", "they", "they'd", "they'll", "they're", 
        "they've", "this", "those", "through", "to", "too", "under", "until", "up", 
        "very", "was", "wasn't", "we", "we'd", "we'll", "we're", "we've", "were", 
        "weren't", "what", "what's", "when", "when's", "where", "where's", "which", 
        "while", "who", "who's", "whom", "why", "why's", "with", "won't", "would", 
        "wouldn't", "you", "you'd", "you'll", "you're", "you've", "your", "yours", 
        "yourself", "yourselves"
    );
    $first_title_word   = '';

    for ( $i = 0; $i < count($title_words); ++ $i ) {
        if ( !in_array($title_words[$i], $stop_words) ) {
            $first_title_word = $title_words[$i];
            break;
        }
    }
    return $last_name. $year. $first_title_word;
}

/**
 * An extension of explode function.
 * @param  array  $delimiters: the array of boundary string
 * @param  string $string: the input string
 * @return an array of strings, each of which is a substring of string formed 
 *         by splitting it on boundaries formed by the delimiter.
 */
function wp_bibtex_multiexplode($delimiters, $string) {
    $ready  = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return $launch;
}

/**
 * Generate the HTML for a highlight item, which includes an featured image for the publication.
 * @param string $bibliography_text: the text of a publication in "Authors. Title. Journal/Conf Name. 
 *               Note." format.
 * @param string $bibtex_text: the BibTeX-format text
 * @param string $additional_fields: the additional fields HTML displayed after the [BibTex] link
 * @param array  $attrs: an array contains attributes of the citation
 * @return a formatted HTML contains expected information for the citation
 */
function wp_bibtex_get_highlight_html($bibliography_text, $bibtex_text, $additional_fields, $attrs) {
    global $wp_bibtex_options;
    $row_class          = $wp_bibtex_options['highlight_row_class'];
    $col_img_class      = $wp_bibtex_options['highlight_img_col_class'];
    $col_desc_class     = $wp_bibtex_options['highlight_desc_col_class'];
    // Fields Format: Authors. Title. Journal/Conf Name. Note.
    $overlay_html       = wp_bibtex_get_overlay_media_html($attrs);
    $img_html           = sprintf('<img src="%s">%s', $attrs['img'], $overlay_html);
    $fields             = explode(".", $bibliography_text);
    $desc_html          = sprintf('<strong>%s</strong><p>%s<br>%s%s</p>', $fields[1], $fields[0],
                                  $fields[2], array_key_exists(3, $fields) ? ("<br>" . $fields[3]) : "");
    return sprintf('<div class="wpbibtex-item-highlight %s"><div class="media %s">%s</div>' .
                   '<div class="%s">%s<ul class="inline">%s<li><a href="javascript:void(0);"' .
                   ' class="wpbibtex-trigger">[BibTeX]</a></li></ul>' .
                   '<div class="bibtex"><pre><code>%s</code></pre></div>' .
                   '</div></div>', 
                   $row_class, $col_img_class, $img_html, $col_desc_class, $desc_html,
                   $additional_fields, $bibtex_text);
}

/**
 * Generate the HTML for the overlaid media.
 * @param array  $attrs: an array contains attributes of the citation
 * @return a formatted HTML contains the overlaid media
 */
function wp_bibtex_get_overlay_media_html($attrs) {
    if (!array_key_exists('overlay', $attrs)) {
        return '';
    }
    $html      = '';
    $media_url = $attrs['overlay'];
    $file_ext  = strtolower(pathinfo($media_url, PATHINFO_EXTENSION));
    if ( $file_ext == "jpg" || $file_ext == "jpeg" ||
         $file_ext == "png" || $file_ext == "webp" ||
         $file_ext == "gif" ) {
        $html  = sprintf('<img src="%s">', $media_url);
    } else if ( $file_ext == "mp4" || $file_ext == "webm" ) {
        $html  = sprintf('<video %s autoplay="" loop="" muted=""><source src="%s" type="video/%s"></video>',
                         (array_key_exists('overlay_fallback', $attrs) ? 'poster="' . $attrs['overlay_fallback'] .'"' : ''),
                         $media_url, $file_ext);
    } else {
        $html  = 'Unknown file extension: ' . $file_ext;
    }
    return sprintf('<div class="overlay transparent">%s</div>', $html);
}

/**
 * Generate the HTML for a legacy publication item.
 * @param string $bibliography_text: the text of a publication in "Authors. Title. Journal/Conf Name. 
 *               Note." format.
 * @param string $bibtex_text: the BibTeX-format text
 * @param string $additional_fields: the additional fields HTML displayed after the [BibTex] link
 * @return a formatted HTML contains expected information for the citation
 */
function wp_bibtex_get_legacy_html($bibliography_text, $bibtex_text, $additional_fields) {
    return sprintf(
        '<div class="wpbibtex-item">%s<ul class="inline">%s<li><a href="javascript:void(0);"' .
        ' class="wpbibtex-trigger">[BibTeX]</a></li></ul><div class="bibtex"><pre><code>' .
        '%s</code></pre></div></div>', $bibliography_text, $additional_fields, $bibtex_text
    );
}

/**
 * Get bibliography style text using citation attributes.
 * @param  array  $attrs: an array contains attributes of the citation
 * @return bibliography style text of the citation
 */
function wp_bibtex_get_bibliography_text($attrs) {
    global $BIBTEX_ENTRIES;
    $citation_type  = $attrs['type'];
    $bibliography   = $BIBTEX_ENTRIES[$citation_type]['bibliography'];
    $bibliography   = preg_replace_callback(
        '|\{\{[a-zA-Z_]+\}\}|',
        function ($matches) use ($attrs) {
            $attr_key       = substr($matches[0], 2, -2);
            $attr_value     = array_key_exists($attr_key, $attrs) ? $attrs[$attr_key] : '';
            if ( $attr_key == 'author' ) {
                $attr_value = wp_bibtex_get_citation_authors_text($attr_value);
            } else if ( $attr_key == 'pages' && !empty($attr_value) ) {
                // Make sure that $attr_value is not empty
                $attr_value = str_replace('--', '-', $attr_value);
            }
            return $attr_value;
        },
        $bibliography
    );
    return preg_replace_callback(
        '|\[[.,;:\-()<>=#\/"\' 0-9a-zA-Z]+\]|',
        function ($matches) {
            // Remove empty optional fields
            if ( preg_match('|\[[.,:\-()pagesVol ]+\]|', $matches[0]) ) {
                return '';
            }
            return substr($matches[0], 1, -1);
        },
        $bibliography
    );
}

/**
 * Convert BibTeX authors field into bibliography style.
 * @param  string $author: the authors of the citation
 * @return bibliography style authors string
 */
function wp_bibtex_get_citation_authors_text($author) {
    global $wp_bibtex_options;
    $blog_owner_name    = $wp_bibtex_options['blog_owner_name'];
    $author_text        = '';
    $authors            = preg_split('/\band\b/u', $author);
    foreach ( $authors as $author ) {
        $names          = mb_split(',', $author);
        $last_name      = trim($names[0]);
        $first_name     = count($names) < 2 ? '' : trim($names[1]);
        $author_name    = sprintf("%s %s", $first_name, $last_name);
        $author_text   .= $author_name. ', ';
    }
    return str_replace(
        $blog_owner_name, 
        sprintf('<strong>%s</strong>', $blog_owner_name), 
        rtrim($author_text, ', ')
    );
}

/**
 * Display additional fields after [BibTex] link.
 * By default, [Download PDF] is displayed.
 * @param  array  $attrs: an array contains attributes of the citation
 * @return a HTML string contains additional fields' links
 */
function wp_bibtex_get_additional_fields($attrs) {
    global $wp_bibtex_options;
    $additional_fields          = $wp_bibtex_options['additional_fields'];
    $additional_fields_text     = '';
    
    foreach ( $additional_fields as $additional_field ) {
        $additional_field_key   = $additional_field['key'];
        $additional_field_name  = $additional_field['value'];
        if ( array_key_exists($additional_field_key, $attrs) ) {
            $additional_field_value = $attrs[$additional_field_key];
            $additional_fields_text.= sprintf("<li><a href='%s' target='_blank'>[%s]</a></li>", 
                $additional_field_value, $additional_field_name);
        }
    }
    return $additional_fields_text;
}

/**
 * Get BibTeX text fot the citation.
 * @param  string $citation_key BibTeX citation key of the citation
 * @param  array  $attrs: an array contains attributes of the citation
 * @return BibTeX text fot the citation
 */
function wp_bibtex_get_bibtex_text($citation_key, $attrs) {
    global $BIBTEX_ENTRIES;
    $BIBTEX_ESCAPE_CHARS    = array('&', '_');
    $BIBTEX_ESCAPED_CHARS   = array('\&', '\_');

    $citation_type          = $attrs['type'];
    $required_fields        = $BIBTEX_ENTRIES[$citation_type]['required'];
    $optional_fields        = $BIBTEX_ENTRIES[$citation_type]['optional'];
    $bibtex_text            = sprintf("@%s{%s", $citation_type, $citation_key);

    foreach ( $required_fields as $field ) {
        $bibtex_text   .= sprintf(",\n  %s={%s}", $field, 
            str_replace($BIBTEX_ESCAPE_CHARS, $BIBTEX_ESCAPED_CHARS, $attrs[$field]));
    }
    foreach ( $optional_fields as $field ) {
        if ( !array_key_exists($field, $attrs) ) {
            continue;
        }
        $bibtex_text   .= sprintf(",\n  %s={%s}", $field, 
            str_replace($BIBTEX_ESCAPE_CHARS, $BIBTEX_ESCAPED_CHARS, $attrs[$field]));
    }
    $bibtex_text       .= sprintf("\n}");
    return $bibtex_text;
}

/**
 * Display essential JavaScript and CSS here.
 */
function wp_bibtex_generate_styles_and_scripts() { 
    wp_enqueue_style('WP-BibTeX', plugins_url('assets/wp-bibtex.css', __FILE__), false, NULL, 'all');
    wp_enqueue_script('WP-BibTeX', plugins_url('assets/wp-bibtex.js', __FILE__), array('jquery'), NULL, true);
}

add_shortcode('WpBibTeX', 'wp_bibtex_shortcode');
add_action('wp_enqueue_scripts', 'wp_bibtex_generate_styles_and_scripts');

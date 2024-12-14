<?php
/**
 * Plugin Name: Ernerdo Contact Form
 * Plugin URI:  https://ernerdo.com
 * Description: Formulario
 * Version:     1.0
 * Author:      Ernerdo
 * Author URI:  https://ernerdo.com
 * License:     GPL2
 */

// Evitar el acceso directo al archivo.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function ecf_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecf_messages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'ecf_create_table' );


function ernerdo_contact_form_shortcode() {
    ob_start();
    ?>
    <style>
        .ecf-form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .ecf-form label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .ecf-form input, .ecf-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .ecf-form input[type="submit"] {
            background-color: #0073aa;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .ecf-form input[type="submit"]:hover {
            background-color: #005f8d;
        }
    </style>

    <form class="ecf-form" method="post" action="">
        <p>
            <label for="ecf-name">Nombre:</label>
            <input type="text" name="ecf-name" id="ecf-name" required>
        </p>
        <p>
            <label for="ecf-email">Correo Electrónico:</label>
            <input type="email" name="ecf-email" id="ecf-email" required>
        </p>
        <p>
            <label for="ecf-message">Mensaje:</label>
            <textarea name="ecf-message" id="ecf-message" rows="5" required></textarea>
        </p>
        <p>
            <input type="submit" name="ecf-submit" value="Enviar">
        </p>
    </form>

    <?php

    if ( isset( $_POST['ecf-submit'] ) ) {
        global $wpdb;

        $name = sanitize_text_field( $_POST['ecf-name'] );
        $email = sanitize_email( $_POST['ecf-email'] );
        $message = sanitize_textarea_field( $_POST['ecf-message'] );

        $table_name = $wpdb->prefix . 'ecf_messages';

        $wpdb->insert(
            $table_name,
            [
                'name'    => $name,
                'email'   => $email,
                'message' => $message
            ],
            [
                '%s', // Tipo: string
                '%s', // Tipo: string
                '%s'  // Tipo: texto
            ]
        );

        echo '<p>Gracias por tu mensaje. Ha sido guardado correctamente.</p>';
    }

    return ob_get_clean();
}

add_shortcode( 'ernerdo_contact_form', 'ernerdo_contact_form_shortcode' );
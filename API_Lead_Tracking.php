
add_action('wp_head', 'hubspot_analytics_tracking');

// Include HubSpot Analytics Tracking Code
function hubspot_analytics_tracking() {
    ?>
    <!-- HubSpot Analytics Tracking Code -->
    <script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/48948311.js"></script>
    <?php
}

// Function to handle form submissions and send data to HubSpot
function send_to_hubspot($form_data) {
    $hubspot_api_key = 'API_KEY'; // Replace with your HubSpot API key
    $hubspot_endpoint = 'https://api.hubapi.com/contacts/v1/contact';

    // Prepare the data for HubSpot
    $data = [
        'properties' => [
            [
                'property' => 'firstname',
                'value' => $form_data['firstname'] ?? ''
            ],
            [
                'property' => 'lastname',
                'value' => $form_data['lastname'] ?? ''
            ],
            [
                'property' => 'email',
                'value' => $form_data['email'] ?? ''
            ],
            [
                'property' => 'message',
                'value' => $form_data['message'] ?? ''
            ]
        ]
    ];

    $args = [
        'body' => json_encode($data),
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $hubspot_api_key"
        ],
        'method' => 'POST'
    ];

    // Send the data to HubSpot
    $response = wp_remote_post($hubspot_endpoint, $args);

    if (is_wp_error($response)) {
        error_log('Error sending data to HubSpot: ' . $response->get_error_message());
    } else {
        error_log('Data sent to HubSpot successfully.');
    }
}

// Hook into WordPress form submission
add_action('wpcf7_mail_sent', 'handle_cf7_submission'); // Example for Contact Form 7 plugin

function handle_cf7_submission($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $form_data = $submission->get_posted_data();
        send_to_hubspot($form_data);
    }
}

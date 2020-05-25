<?php

# Sample code for the WhatIsMyBrowser.com API - Version 2
#
# User Agent Parse
# This sample code provides a very straightforward example of
# sending an authenticated API request to parse a user agent
# and display some basic results to the console.
#
# It should be used as an example only, to help you get started
# using the API. This code is in the public domain, feel free to
# take it an integrate it with your system as you require.
# Refer to the "LICENSE" file in this repository for legal information.
#
# For further documentation, please refer to the Integration Guide:
# https://developers.whatismybrowser.com/api/docs/v2/integration-guide/
#
# For support, please refer to our Support section:
# https://developers.whatismybrowser.com/api/support/

# Your API Key
# You can get your API Key by following these instructions:
# https://developers.whatismybrowser.com/api/docs/v2/integration-guide/#introduction-api-key
$api_key = "";

# An example user agent to parse:
$user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36";

# Where will the request be sent to
$url = 'https://api.whatismybrowser.com/api/v2/user_agent_parse';

# -- Set up HTTP Headers
$headers = [
    'X-API-KEY: '.$api_key,
];

# -- prepare data for the API request
# This shows the `parse_options` key with some options you can choose to enable if you want
# https://developers.whatismybrowser.com/api/docs/v2/integration-guide/#user-agent-parse-parse-options
$post_data = array(
    "user_agent" => $user_agent,
    "parse_options" => array(
        #"allow_servers_to_impersonate_devices" => True,
        #"return_metadata_for_useragent" => True,
        #"dont_sanitize" => True,
    )
);

# -- create a CURL handle containing the settings & data
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

# -- Make the request
$result = curl_exec($ch);
$curl_info = curl_getinfo($ch);
curl_close($ch);

# -- Try to decode the api response as json
$result_json = json_decode($result);
if ($result_json === null) {
    echo "Couldn't decode the response as JSON\n";
    exit();
}

# -- Check that the server responded with a "200/Success" code
if ($curl_info['http_code'] != 200) {
    echo "ERROR: not a 200 result. instead got: ".$curl_info['http_code'].".\n";
    var_dump($result_json);
    exit();
}

# -- Check the API request was successful
if ($result_json->result->code != "success") {
    echo "The API did not return a 'success' response. It said: result code: ".$result_json->result->code.", message_code: ".$result_json->result->message_code.", message: ".$result_json->result->message."\n";
    exit();
}

# Now you have "$result_json" and can store, display or process any part of the response.

# -- print the entire json dump for reference
var_dump($result_json);

# -- Copy the data to some variables for easier use
$parse = $result_json->parse;
$version_check = $result_json->version_check;

# Now you can do whatever you need to do with the parse result
# Print it to the console, store it in a database, etc
# For example - printing to the console:

if ($parse->simple_software_string) {
    echo $parse->simple_software_string."\n";
}
else {
    echo "Couldn't figure out what software they're using\n";
}

if ($parse->simple_sub_description_string) {
    echo $parse->simple_sub_description_string."\n";
}

if ($parse->simple_operating_platform_string) {
    echo $parse->simple_operating_platform_string."\n";
}

if ($version_check) {
    # Your API account has access to version checking information

    if ($version_check->is_checkable === True) {
        if ($version_check->is_up_to_date === True) {
            echo $parse->software_name." is up to date\n";
        }
        else {
            echo $parse->software_name." is out of date\n";

            if ($version_check->latest_version) {
                echo "The latest version is ".join(".", $version_check->latest_version)."\n";
            }

            if ($version_check->update_url) {
                echo "You can update here: ".$version_check->update_url."\n";
            }
        }
    }
}

# Refer to:
# https://developers.whatismybrowser.com/api/docs/v2/integration-guide/#user-agent-parse-field-definitions
# for descriptions of all the fields you can access and what they are used for.

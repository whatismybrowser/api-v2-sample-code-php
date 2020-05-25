<?php

# Sample code for the WhatIsMyBrowser.com API - Version 2
#
# User Agent Parse Batch
# This sample code provides a very straightforward example of
# sending an authenticated API request to parse a batch of user agents
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

# -- Set up the request data
# Some sample user agents to send in a batch
$user_agents = array(
    "1" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.155 Safari/537.36",
    "2" => "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36",
    "3" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/600.7.12 (KHTML, like Gecko) Version/8.0.7 Safari/600.7.12",
    "4" => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36",
    "5" => "Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.5 Mobile/15E148 Safari/604.1",
    "6" => "Mozilla/5.0 (PlayStation 4 5.55) AppleWebKit/601.2 (KHTML, like Gecko)",
    "7" => "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
    "8" => "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko",
);

# Where will the request be sent to
$url = 'https://api.whatismybrowser.com/api/v2/user_agent_parse_batch';

# -- Set up HTTP Headers
$headers = [
    'X-API-KEY: '.$api_key,
];


# -- prepare data for the API request
# This shows the `parse_options` key with some options you can choose to enable if you want
# https://developers.whatismybrowser.com/api/docs/v2/integration-guide/#user-agent-parse-parse-options
$post_data = array(
    "user_agents" => $user_agents,
    "parse_options" => array(
        #"allow_servers_to_impersonate_devices" => True,
        #"return_metadata_for_useragent" => True,
        #"dont_sanitize" => True,
    )
);

if (count($user_agents) > 500) {
    echo "You are attempting to send more than the maximum number of user agents in one batch\n";
    exit();
}

echo "Processing ".count($user_agents)." user agents in one batch. Please be patient.\n";

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

# -- Display some basic info about each parse result in the list
foreach ($result_json->parses as $parse_key => $individual_parse) {

    # The whole result from the batch is now in $individual_parse

    # This includes the `parse` dict, as well as `result`.
    # At this point - inside the loop - it's basically the same as working with
    # an individual user agent parse (as is done in user_agent_parse.py). There
    # is a `result`, `parse` and possibly a `version_check` and `user_agent_metadata` etc

    # Remember, the JSON will probably not be in the same order as you sent it,
    # so you need to match each key in `parses` (`parse_id`) back to the key you sent through.

    if ($individual_parse->result->code != "success") {
        echo "There was a problem parsing the user agent with the id ".$parse_key."\n";
        echo $individual_parse->result->message."\n";
        continue;  # to the next record in the batch
    }

    # -- Print the individual parse result for this record
    #var_dump($individual_parse);

    # -- Now copy the actual parse result to a different variable for easier use
    $parse = $individual_parse->parse;

    # You can now access the parse results in the `parse` dict and use them however you would like.
    # For example:
    echo $parse_key.": [".$parse->hardware_type."/".$parse->software_type."] ".$parse->simple_software_string."\n";

    # Refer to:
    # https://developers.whatismybrowser.com/api/docs/v2/integration-guide/#user-agent-parse-field-definitions
    # for descriptions of all the fields you can access and what they are used for.

}  # end of the foreach loop


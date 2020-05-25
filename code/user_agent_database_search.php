<?php

# Sample code for the WhatIsMyBrowser.com API - Version 2
#
# User Agent Database Search
# This sample code shows you how to search the database for useragents
# which match your query. It's not for decoding/parsing user agents,
# you should use the User Agent Parse API Endpoint instead.
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

# The various search parameters
# This is a basic search for Safari user agents... but it includes
# other sample parameters which have been commented out. Change the
# parameters which get sent to fetch the results you need.
#
# You can also use the Web Based form to experiment and see which
# parameter values are valid:
# https://developers.whatismybrowser.com/api/docs/v2/sample-code/database-search

$search_params = array(
    "software_name" => "Safari",  # "Internet Explorer" "Chrome" "Firefox"
    #"software_version" => "71",
    #"software_version_min" => "64",
    #"software_version_max" => "79",

    #"operating_system_name" => "macOS", # "OS X", "Linux", "Android", "iOS" etc
    #"operating_system_version" => "Snow Leopard", # "Vista", "8.2" etc

    #"operating_platform" => "iPhone",  # "iPhone 5", "Galaxy Gio", "Galaxy Note", "Galaxy S4"
    #"operating_platform_code" => "GT-S5660",

    #"software_type" => "browser", # "bot" "application"
    #"software_type_specific" => "web-browser",  # "in-app-browser", "analyser" "application" "bot" "crawler" etc

    #"hardware_type" => "computer", # "computer" "mobile" "server"
    #"hardware_type_specific" => "computer", # "phone", "tablet", "mobile", "ebook-reader", "game-console" etc

    #"layout_engine_name" => "NetFront", # Blink, Trident, EdgeHTML, Gecko, NetFront, Presto

    #"order_by" => "times_seen desc",  # "times_seen asc" "first_seen_at asc" "first_seen_at desc" "last_seen_at desc" "last_seen_at asc" "software_version desc"
    #"times_seen_min" => 100,
    #"times_seen_max" => 1000,
    #"limit" => 250,
);


# Where will the request be sent to
$url = "https://api.whatismybrowser.com/api/v2/user_agent_database_search?". http_build_query($search_params);

# -- Set up HTTP Headers
$headers = [
    'X-API-KEY: '.$api_key,
];

# -- create a CURL handle containing the settings & data
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
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
    echo "Didn't receive a 200 Success response from the API\n";
    echo "Instead, there was a ".$curl_info['http_code']." code\n";
    echo "The message was: ".$result_json->result->message."\n";
    exit();
}

# -- Check the API request was successful
if ($result_json->result->code != "success") {
    throw new Exception("The API did not return a 'success' response. It said: result: ".$result_json->result.", message_code: ".$result_json->message_code.", message: ".$result_json->message_code);
    exit();
}

# Now you have "$result_json" and can store, display or process any part of the response.

# -- print the entire json dump for reference
var_dump($result_json);

# -- Copy the `user_agents` search results data to a variable for easier use
$returned_user_agents = $result_json->search_results->user_agents;

foreach ($returned_user_agents as $returned_user_agent) {
    echo $returned_user_agent->user_agent." - seen: ".$returned_user_agent->user_agent_meta_data->times_seen." times"."\n";
}

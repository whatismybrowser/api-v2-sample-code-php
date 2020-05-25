<?php

# Sample code for the WhatIsMyBrowser.com API - Version 2
#
# Database Dump Url
# This sample code provides an example of querying the API
# to get the URLs of the latest user agent database dump.
# The database is not for decoding/parsing user agents,
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


# choose the format you want to download by uncommenting it
$file_format = "mysql";
#$file_format = "csv";
#$file_format = "txt";

# Where will the request be sent to
$url = "https://api.whatismybrowser.com/api/v2/user_agent_database_dump_url?file_format=". $file_format;

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

# -- Copy the `user_agent_database_dump` data to a variable for easier use
$user_agent_database_dump =  $result_json->user_agent_database_dump;

echo "You requested the ".$file_format." data format.\n";
echo "The latest data file contains ".$user_agent_database_dump->num_of_useragents." user agents\n";
echo "You can download it from: ".$user_agent_database_dump->url ."\n";

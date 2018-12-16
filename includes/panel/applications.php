<?php

function applications_list($user_id)
{
    echo '<style>.collection .collection-item.avatar{min-height:0;}</style>';
    echo '<ul class="collection">';

    $user_applications = json_decode(sql_select('users', 'applications', "id='{$user_id}'", true)['applications']);

    foreach ($user_applications as $user_application) {
        $client = sql_select('clients', 'redirect_url,user_id,name,logo_url,description,suspended', "client_id='{$user_application}'", true);
        $owner = sql_select('users', 'username', "id='{$client['user_id']}'", true);

        print_r($user_application);

        echo <<<HTML
        <li class="collection-item avatar">
            <a href="?application={$user_application}">
                <img class="circle" src="{$client['logo_url']}" onerror="this.src='https://github.com/identicons/{$user_application}.png'"> <span class="title">{$client['name']}</span>
            </a>
                <p>Owned by <span class="blue-text">{$owner['username']}</span></p>
        </li>
HTML;
    }

    echo '</ul>';
}


function application_info($user_id, $client_id)
{
    $client = clean_data($client_id);
    $client = sql_select('clients', 'redirect_url,user_id,name,logo_url,description,suspended', "client_id='{$client_id}'", true);

    $CSRFtoken = csrf_gen();

    $callFunction = function ($function) {
        return $function;
    };

    echo <<<HTML
    <style>
        .mb-0{margin-bottom:0}
        .mt-1_52rem{margin-top:1.52rem}
    </style>
    <div class="row mb-0">
        <div class="col s12 m2">
            <img class="responsive-img" src="{$client['logo_url']}" onerror="this.src='https://github.com/identicons/{$client_id}.png'" width="75">
        </div>
        <div class="col s12 m10">
            <h4>
                {$client['name']}
            </h4>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col s12 m9">
            <h4>
                Permissions
            </h4>
        </div>
        <div class="col s12 m3">
            <a href="?revoke={$client_id}&CSRFtoken={$CSRFtoken}" class="btn waves-effect red accent-4 mt-1_52rem" onclick="return confirm('Are you sure?')">Revoke Access</a>
        </div>
    </div>
    <div class="row">
        <ul class="browser-default">
            {$callFunction(application_list_scopes($user_id, $client_id))}
        </ul>
    </div>
    <div class="row">
        <a href="/home"><i class="material-icons">arrow_back</i> Go back</a>
    </div>
HTML;
}


function application_list_scopes($user_id, $client_id)
{
    $user_applications = json_decode(sql_select('users', 'applications', "id='{$user_id}'", true)['applications']);

    foreach ($user_applications[$client_id] as $scope) {
        $scope_data = sql_select('scopes', 'title,description', "scope='{$scope}'", true);
        echo <<<HTML
        <li>
            <p><b>{$scope_data['title']}</b></p>
            <p>{$scope_data['description']}</p>
        </li>
HTML;
    }
}


function application_revoke($user_id, $client_id, $CSRFtoken)
{
    csrf_val($CSRFtoken);

    $user_applications = json_decode(sql_select('users', 'applications', "id='{$user_id}'", true)['applications']);

    if (in_array($client_id, $user_applications)) {
        $index = array_search($client_id, $user_applications);
        if ($index !== false) {
            unset($user_applications[$index]);
        }

        $user_applications = json_encode($user_applications);

        sql_update('users', ['applications' => $user_applications], "id='{$user_id}'");
    }

    sql_delete('authorization_codes', "client_id='{$client_idAND}' user_id='{$user_id}'");

    sql_delete('access_tokens', "client_id='{$client_idAND}' user_id='{$user_id}'");
}

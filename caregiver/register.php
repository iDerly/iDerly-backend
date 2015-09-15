<?php
/*
### Register
```
POST /caregiver/register
```

#### Parameters
* `email`
* `password`
* `name`
* `user_id`

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of success/error messages

*/
$this->respond('POST', '/?', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $password = $mysqli->escape_string($request->param('password'));
    $name = $mysqli->escape_string($request->param('name'));
    $email = $mysqli->escape_string($request->param('email'));
    $user_id = $mysqli->escape_string($request->param('user_id'));

    // error checking
    if (strlen($password) < 6)         $service->flash("Your password must be more than 6 characters.", 'error');
    if (is_empty(trim($name)))         $service->flash("Please enter your full name.", 'error');
    if (is_empty(trim($email)))        $service->flash("Please enter your e-mail address.", 'error');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                                        $service->flash("Please enter a valid e-mail address.", 'error');
    if (is_empty(trim($user_id)))      $service->flash("Please enter your user_id.", 'error');


    $num_rows = 0;
    $sql_query = "SELECT * FROM `caregiver` WHERE `email` = ?";
    $stmt = $mysqli->prepare($sql_query);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $res = $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;

        $stmt->close();
    }
    if ($num_rows === 1) {
        $service->flash("E-mail already in use, please use another e-mail.", 'error');
    }
    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        $password = hash('sha512',hash('whirlpool', $password));
        $sql_query = "INSERT INTO user(`password`, `email`, `user_id`)
                    VALUES(?, ?, ?)";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("ssi", $password, $email, $user_id);
            $res = $stmt->execute();
            if ($res) {
                $service->flash("Caregiver successfully registered.", 'success');
                $return['status'] = 0;
                $return['message'] = $service->flashes('success');
            } else {
                $service->flash("Failed to insert data to database: " . $stmt->error, 'error');
                $return['status'] = -1;
                $return['message'] = $service->flashes('error');
            }
            $stmt->close();
        } else {
            $service->flash("SQL statement error ", 'error');
            $return['status'] = -1;
            $return['message'] = $service->flashes('error');
        }
    } else {
        $return['status'] = -1;
        $return['message'] = $error_msg;
    }
    return json_encode($return);
});

<?php
/*
### Add photo
```
POST /elder/add_photo
```

#### Parameters
* `attachment`: base-64 encoded string of the photo
* `user_id`

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of success/error messages

*/
$this->respond('POST', '/?', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $attachment = $mysqli->escape_string($request->param('attachment'));
    $user_id = $mysqli->escape_string($request->param('user_id'));

    // error checking
    if (is_empty(trim($attachment)))      $service->flash("Please enter your attachment.", 'error');
    if (is_empty(trim($user_id)))      $service->flash("Please enter your user_id.", 'error');


    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        $sql_query = "INSERT INTO photo(`attachment`, `user_id`)
                      VALUES(?, ?)";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("si", $attachment, $user_id);
            $res = $stmt->execute();
            if ($res) {
                $service->flash("Photo added", 'success');
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

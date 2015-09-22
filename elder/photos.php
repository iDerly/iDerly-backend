<?php
/*
### Get photos stored by elders
```
REQUEST /elder/photos/[i:user_id]
```

#### Parameters
* `user_id`

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of error messages; or list of (photo_id, base-64 encoded image, attachment, remarks, #appear, #correct)

*/
$this->respond('/[i:user_id]', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id = $mysqli->escape_string($request->param('user_id'));

    // error checking
    if (is_empty(trim($user_id)))     $service->flash("Please enter the user_id.", 'error');    

    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        $sql_query = "SELECT `id`, `attachment`, `name`, `remarks`, `appear`, `correct` FROM `photo` WHERE `user_id` = ? LIMIT 0,1000";
        $stmt = $mysqli->prepare($sql_query);
        $stmt->bind_param("i", $user_id);
        $res = $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($photo_id, $attachment, $name, $remarks, $appear, $correct);

        $return_msg = [];
        while ($stmt->fetch()) {
            array_push($return_msg, array(
                "photo_id" => $photo_id,
                "attachment" => $attachment,
                "name" => $name,
                "remarks" => $remarks,
                "appear" => $appear,
                "correct" => $correct
            ));
        }


        // http://stackoverflow.com/a/6061602/917957
        // $img = base64_decode($attachment);
        // $f = finfo_open();

        // $mime_type = finfo_buffer($f, $img, FILEINFO_MIME_TYPE);

        // header('Content-Type: '. $mime_type);
        // return $img;
        $return['status'] = 0;
        $return['message'] = $return_msg;
        return json_encode($return);

    } else {
        $return['status'] = -1;
        $return['message'] = $error_msg;
        return json_encode($return);
    }
    
});

<?php
/*
### Get elder photos
```
REQUEST /elder/photos/[i:user_id]
```

#### Parameters
* `user_id`

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of error messages; or list of base-64 encoded images

*/
$this->respond('/[i:user_id]', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id = $mysqli->escape_string($request->param('user_id'));

    // error checking
    if (is_empty(trim($user_id)))     $service->flash("Please enter the user_id.", 'error');    

    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        $sql_query = "SELECT `attachment` FROM `photo` WHERE `user_id` = ? LIMIT 0,100";
        $stmt = $mysqli->prepare($sql_query);
        $stmt->bind_param("i", $user_id);
        $res = $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($attachment);

        $return_attachment = [];
        while ($stmt->fetch()) {
            array_push($return_attachment, $attachment);
        }


        // http://stackoverflow.com/a/6061602/917957
        // $img = base64_decode($attachment);
        // $f = finfo_open();

        // $mime_type = finfo_buffer($f, $img, FILEINFO_MIME_TYPE);

        // header('Content-Type: '. $mime_type);
        // return $img;
        $return['status'] = 0;
        $return['message'] = $return_attachment;
        return json_encode($return);

    } else {
        $return['status'] = -1;
        $return['message'] = $error_msg;
        return json_encode($return);
    }
    
});

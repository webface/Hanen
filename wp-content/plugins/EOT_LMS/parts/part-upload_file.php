<?php
global $current_user;
$user_id = $current_user->ID; // Wordpress user ID
$org_id = get_org_from_user($user_id); // Organization ID

$dir = plugin_dir_path(__FILE__);
require $dir . 'aws/aws-autoloader.php';
$admin_ajax_url = admin_url('admin-ajax.php');
?>
<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <span class="current">Upload File</span>     
</div>

<?php
if(current_user_can( "is_director" )){
    
?>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.24.min.js"></script>
<script>
    var filekey,filetype;
    $(document).ready(function () {
        $.ajax({url: ajax_object.ajax_url + "?action=getAwsCredentials", success: function (result) {
                //console.log(result);
                var result = JSON.parse(result);
                AWS.config.update({
                    accessKeyId: result.accessKeyId,
                    secretAccessKey: result.secretAccessKey
                });
                AWS.config.region = 'us-west-2';
                //console.log(AWS.config);
                //add upload button after page has loaded
                $("#fileUploadForm").append('<input type="submit" value="Upload File" name="submit" class="pull-right">');
                $("#fileUploadForm").submit(function () {
                    var bucket = new AWS.S3({params: {Bucket: 'eot-resources'}});
                    var fileChooser = document.getElementById('file');
                    var file = fileChooser.files[0];
                    console.log(Files);
                    for (var i = 0; i < Files.length; i++) {
                        if (Files[i].file_key ==<?= $org_id; ?> + "_" + file.name) {
                            alert('You have already uploaded this file!');
                            return false;
                        }
                    }
                    if ($('input[name="title"]').val() == "") {
                        alert('Please give a title to your file. It is what your users will see.');
                        return false;
                    }


                    console.log(file);
                    if (file) {
                        filekey =<?= $org_id; ?> + "_" + file.name;
                        filetype=file.type;
                        var params = {Key: <?= $org_id; ?> + "_" + file.name, ContentType: file.type, Body: file};
                        bucket.upload(params).on('httpUploadProgress', function (evt) {
                            $(".progress-bar").html(parseInt((evt.loaded * 100) / evt.total) + "%");
                            $(".progress-bar").css("width", parseInt((evt.loaded * 100) / evt.total) + '%');
                            //console.log("Uploaded :: " + parseInt((evt.loaded * 100) / evt.total) + '%');

                        }).send(function (err, data) {
                            if (err == null) {
                                console.log(data);
                                submit_upload(data);
                                $("form").get(0).reset();
                                //$(".progress-bar").css("width", "0%");
                            } else {
                                alert(err);
                                $("form").get(0).reset();
                                //$(".progress-bar").css("width", "0%");
                            }
                        });
                    }
                    return false;
                });
                $('[data-toggle="tooltip"]').tooltip();
            }});
        $("#urlSaveForm").submit(function (e) {
            e.preventDefault();
            if ($('input[name="title2"]').val() == "") {
                alert('Please give a title to your file. It is what your users will see.');
                return false;
            }
            if ($('input[name="url"]').val() == "") {
                alert('Please provide the url to download your file.');
                return false;
            }
            var data = {
                'action': 'save_url',
                'user_id': <?= $user_id; ?>,
                'org_id': <?= $org_id; ?>,
                'url': $('input[name="url"]').val(),
                'key': 'url',
                'title': $('input[name="title2"]').val()
                , };
            console.log(data);
            $.ajax({
                url: eot.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (data) {
                    console.log(data);
                    if (data.message == "success") {
                        //alert("success");
                        $("#urlSaveForm").get(0).reset();
                        load('load_processing');
                        location.reload();
                    } else {
                        alert("There was an error saving your data");
                    }
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                    alert("There was an error saving your data");
                }
            });
            return false;
        });

    });
    function submit_upload(data) {
        var data = {
            'action': 'upload_file',
            'user_id': <?= $user_id; ?>,
            'org_id': <?= $org_id; ?>,
            'url': data.Location,
            'key': filekey,
            'type':filetype,
            'title': $('input[name="title"]').val()
        };
        console.log(data);
        //return;
        $.ajax({
            url: eot.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (data) {
                console.log(data);
                if (data.message == "success") {
                    load('load_processing');
                    location.reload();
                } else {
                    alert("There was an error saving your data");
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown);
                alert("There was an error saving your data");
            }
        });

    }
    $(document).bind('success.update_file_title',
            function (event, data)
            {
                if (data.success === true) {
                    location.reload();
                }
            }
    )
    function updateFileTitle(id, title) {
        $.facebox(function () {

            $.ajax({
                data: {'title': title, 'id': id},
                error: function () {
                    $.facebox('There was an error loading the title. Please try again shortly.');
                },
                success: function (data) {
                    $.facebox(data);
                },
                type: 'post',
                url: '<?= $admin_ajax_url; ?>?action=get_upload_form&form_name=update_title'
            });

        });

    }
    $(document).bind('success.aws_delete_file',
            function (event, data)
            {
                if (data.success === true) {
                    location.reload();
                }
            }
    )
    function deleteFile(id, key) {
        $.facebox(function () {

            $.ajax({
                data: {'id': id, 'key': key},
                error: function () {
                    $.facebox('There was an error deleting the file. Please try again shortly.');
                },
                success: function (data) {
                    $.facebox(data);
                },
                type: 'post',
                url: '<?= $admin_ajax_url; ?>?action=get_upload_form&form_name=delete_file'
            });

        });

    }
</script>
<?php
global $wpdb;
$files =  getUserUploads($org_id, $user_id);
;
?>
<script>var Files =<?= json_encode($files); ?></script>
<h1 class="article_page_title">Manage Your Uploaded Resources</h1>
<h3 class="article_page_title">My Files</h3>
<div class="bs">
    <table class="table table-hover table-bordered table-striped files">
        <thead>
            <tr>
                <td>Title</td>
                <td>File/URL</td>
                <td>Type</td>
                <td>Actions</td>
            </tr>
        <tbody>
            <?php
            if (count($files) > 0) {
                foreach ($files as $file) {
                    ?>
                    <tr id="file_<?= $file['ID']; ?>">
                        <td><?= $file['name']; ?></td>
                        <td>
                            <?php
                            if ($file['file_key'] == 'url') {
                                echo $file['url'];
                            } else {
                                $split = explode($org_id . '_', $file['file_key']);
                                echo $split[1];
                            }
                            ?>
                        </td>
                        <td><?= $file['type']; ?></td>
                        <td>
                            <a href="#" onclick="updateFileTitle(<?= $file['ID']; ?>, '<?= $file['name']; ?>')" title="Update Title" data-toggle="tooltip" data-placement="bottom"><span class="fa fa-pencil"></span></a>&nbsp;
                            <?php if ($file['file_key'] != 'url') { ?>
                                <a href="<?= $file['url']; ?>" target="_blank" title="Download File" data-toggle="tooltip" data-placement="bottom"><span class="fa fa-download"></span></a>&nbsp;
                            <?php } ?>
                            <a href="#" onclick="deleteFile(<?= $file['ID']; ?>, '<?= $file['file_key']; ?>')" title="Delete File" data-toggle="tooltip" data-placement="bottom"><span class="fa fa-close"></span></a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td>No Resources</td></tr>';
            }
            ?>
        </tbody>
        </thead>
    </table>
</div>
<h3 class="article_page_title">Upload Files</h3>
<div class="bs">
<form id="fileUploadForm" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" class="bs form-control" name="title" />
    </div>
    <input type="file" name="file" id="file" value="dataFile" required="" class="bs pull-left">

</form>
<span class="clearfix"></span>

    <div class="progress" style="">
        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;background-color:green;color:white">
            0%
        </div>
    </div>

<h3 class="article_page_title">Create Link as a Resource</h3>
<form id="urlSaveForm" method="post">
    <div class="form-group">
        <label for="title2">Title</label>
        <input type="text" class=" form-control" name="title2" />
    </div>
    <div class=" form-group">
        <label for="url">URL</label>
        <input type="url" class=" form-control" name="url" value="http://" />
    </div>
    <input type="submit"  class=" pull-left" value="Submit">

</form>
<span class="clearfix"></span>
</div>
<div class="files">
    <?php
// Instantiate the S3 client with your AWS credentials
    $s3Client = new Aws\S3\S3Client(array(
        'version' => 'latest',
        'region' => 'us-west-2',
        'credentials' => array(
            'key' => 'AKIAJQVK5SFFHTTQBEJQ',
            'secret' => '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',
        )
    ));

    try {
        $objects = $s3Client->getIterator('ListObjects', array(
            'Bucket' => 'eot-resources',
            'region' => 'us-west-2',
            'Prefix' => $org_id . "_"
        ));
        foreach ($objects as $object) {
            //echo $object['Key'] . "<br>";
        }
    } catch (S3Exception $e) {
        echo $e->getMessage() . "<br>";
    }
    ?>
</div>

<?php 
}
else
        {
            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
            return;
          }

?>
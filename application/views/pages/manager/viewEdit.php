<!-- DataTables -->
<link rel="stylesheet"
    href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Manager edit</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="email" placeholder="email" value="<?php echo $data['email']; ?>"
                                    readonly>
                            </div>
                            <input id="id" name="id" type="hidden" class="form-control" placeholder="id"
                                value="<?php echo $data['id']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-8">
                                <input id="password" type="text" class="form-control" placeholder="password"
                                    value="<?php echo $data['password']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Org_id</label>
                            <div class="col-sm-8">
                                <input id="org_id" type="text" class="form-control" placeholder="org_id"
                                    value="<?php echo $data['org_id']; ?>">
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="button" class="btn btn-default pull-right" onclick="onSend()">Done</a>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title" id="cameras_title">Cameras</h3>
                    <!-- <div class="pull-right">
                        <button type="button" class="btn btn-default" data-toggle="modal"
                            data-target="#modal-camera-add">
                            Add Camera
                        </button>
                        <button type="button" id="buttonLabelCameras" class="btn btn-default">Label Cameras</button>
                    </div> -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tableCamera" class="table table-bordered table-striped" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                                <th>Type</th>
                                <th>Carrier</th>
                                <th>IMEI</th>
                                <th>iccid</th>
                                <th>Activate</th>
                                <th>Usage</th>
                                <th>created_at</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>

    <div class="modal modal-primary fade" id="modal-camera-add">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add a camera</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-8">
                                    <input id="input_camera_name" type="text" class="form-control"
                                        placeholder="Camera's Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">IMEI</label>
                                <div class="col-sm-8">
                                    <input id="input_camera_IMEI" type="text" maxlength="15"
                                        oninput="validateInput(this)" class="form-control" placeholder="IMEI">
                                    <p id="error" style="color: white;"></p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline" id="button-add-camera">Add</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</section>

<!-- DataTables -->
<script
    src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script
    src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
    var user_id = "<?php echo $data['user_id']; ?>"
    function onSend() {

        var id = document.getElementById('id').value
        var email = document.getElementById('email').value
        var password = document.getElementById('password').value
        var org_id = document.getElementById('org_id').value

        $.post('<?php echo base_url() ?>manager/edit', {
            id: id,
            password: password,
            org_id: org_id,
            email: email,
        }, function (data, status) {
            var obj = JSON.parse(data);
            if (obj.result == 'ok') {
                window.history.back();
            } else {
                alert(obj.message);
            }
        });

        return false;
    }

    function validateInput(input) {
        let errorMessage = document.getElementById("error");

        // Remove non-numeric characters
        input.value = input.value.replace(/\D/g, '');

        // Check if the length is exactly 15
        if (input.value.length > 15) {
            input.value = input.value.slice(0, 15);
        }

        if (input.value.length < 15) {
            errorMessage.textContent = "Please enter exactly 15 digits.";
        } else {
            errorMessage.textContent = "";
        }
    }

    function usageString(data) {
        var dataString = "0B"
        if (data > 1000000000) {
            var value = (data / 1000000000).toFixed(1)
            dataString = value + "GB"
        } else if (data > 1000000) {
            var value = (data / 1000000).toFixed(1)
            dataString = value + "MB"
        } else if (data > 1000) {
            var value = (data / 1000).toFixed(1)
            dataString = value + "KB"
        } else if (data > 1) {
            var value = data
            dataString = value + "B"
        }
        return dataString;
    }

    var tableCamera = $('#tableCamera').DataTable({
            "autoWidth": true,
            "width": '100%',
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "pageLength": 10,
            searching: false,
            "order": [
                [0, "asc"]
            ],
            "ajax": {
                url: `${base_url}camera/getListByUser`,
                data: function (d) {
                    Object.assign(d, {
                        "user_id": user_id,
                    });
                    return d;
                },
                type: "POST",
                "dataSrc": function (json) {
                    var total_usage = 0;
                    for (var i = 0; i < json.data.length; i++) {
                        if (json.data[i].data_usage > 1)
                            total_usage += parseInt(json.data[i].data_usage, 10);
                    }

                    $("#cameras_title").text("Cameras - Data Usage: " + usageString(total_usage));
                    return json.data;
                }
            },

            "columns": [{
                'data': 'id',
                className: 'text-center',
            },
            {
                'data': 'name',
                className: 'text-center',
            },
            {
                'data': 'version',
                className: 'text-center',
            },
            {
                'data': 'att_verizon',
                className: 'text-center',
            },
            {
                'data': 'IMEI',
                className: 'text-center',
            },
            {
                'data': 'iccid',
                className: 'text-center',
            },
            {
                'data': 'is_active',
                className: 'text-center',
            },
            {
                'data': 'data_usage',
                className: 'text-center',
            },
            {
                'data': 'created_at',
                className: 'text-center',
            },
            ],

            columnDefs: [{
                "width": "10%",
                "targets": 2,
                render: function (data, type, row) {
                    var strData = data;
                    if (data == "A") {
                        strData = "Standard"
                    } else if (data == "B") {
                        strData = "Mini"
                    } else if (data == "C") {
                        strData = "DataCam"
                    }
                    return strData;
                }
            }, {
                "width": "10%",
                "targets": 3,
                render: function (data, type, row) {
                    return data;
                }
            },
            {
                "width": "10%",
                "targets": 4,
                render: function (data, type, row) {
                    if (row.IMEI != "") {
                        const html = '<a type="button" class="fa fa-edit edit-IMEI"  data-imei="' + row.IMEI + '" data-id="' + row.id + '">' + data + '</a>';
                        return html;
                    }
                    return "";
                }
            },
            {
                "width": "10%",
                "targets": 5,
                render: function (data, type, row) {
                    return data
                }
            },
            {
                "width": "10%",
                "targets": 6,
                render: function (data, type, row) {
                    if (row.IMEI != "") {
                        if (data == 0) {
                            return '<button class="btn btn-danger btn-activate" data-imei="' + row.IMEI + '">Suspended</button>';
                        }
                        return '<button class="btn btn-success btn-deactivate" data-imei="' + row.IMEI + '">Activated</button>';
                    }
                    return "";
                }
            },
            {
                "width": "10%",
                "targets": 7,
                "orderable": false,
                render: function (data, type, row) {
                    return usageString(data);
                }
            },
            {
                "targets": 9,
                "orderable": false,
                render: function (data, type, row) {
                    if (row.IMEI != "") {
                        const linkurl = `${base_url}camera/viewEdit/${row.device_id}`;
                        const html = `<i class="fa fa-edit" data-link = "${linkurl}"></i>`;
                        return html;
                    }
                    return "";
                }
            },
            {
                "targets": 10,
                "orderable": false,
                render: function (data, type, row) {
                    const html = `<td class="delete"><i class="fa fa-trash fa-remove" data-id="${row.id}"></td>`;
                    return html;
                }
            },
            ],
        })

        $('#button-add-camera').click(function () {
            var camera_name = $("#input_camera_name").val()
            var camera_IMEI = $("#input_camera_IMEI").val()
            var url = base_url + 'user/add_camera'
            $.post(url, {
                user_id: user_id,
                camera_name: camera_name,
                camera_IMEI: camera_IMEI
            }, function (data, status) {
                var obj = JSON.parse(data);
                if (obj.result == 'ok') {
                    $("#input_camera_name").val('')
                    $("#input_camera_IMEI").val('')
                    $('#modal-camera-add').modal('hide');
                    tableCamera.ajax.reload(null, false);
                } else {
                    alert(obj.message);
                }
            })
            return false;
        });
</script>
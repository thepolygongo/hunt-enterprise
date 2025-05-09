<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Create a special user for promoters, advertisers and developers</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" action="<?php echo base_url() ?>user/create" method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-1 control-label">Username</label>
                            <div class="col-sm-4">
                                <input type="username" name="username" class="form-control input100" placeholder="username" required minlength="4">
                            </div>
                            <label class="col-sm-1 control-label">Email</label>
                            <div class="col-sm-4">
                                <input type="email" name="email" class="form-control input100" placeholder="Email" required minlength="4">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-1 control-label">Password</label>
                            <div class="col-sm-4">
                                <input type="password" name="password" class="form-control input100" placeholder="Password" required minlength="4">
                            </div>
                            <label class="col-sm-1 control-label">Confirm</label>
                            <div class="col-sm-4">
                                <input type="password" name="password1" class="form-control input100" placeholder="Confirm Password" required minlength="4">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-1 control-label">Type</label>
                            <div class="col-sm-4">
                            <input type="account_type" name="account_type" class="form-control input100" value="pro_user" readonly>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn btn-info pull-right" onclick="onSend()">Submit</button>
                    </div>
                    <!-- /.box-footer -->
                </form>

            </div>
            <!-- /.box -->
        </div>
    </div>
</section>

<script>
    function onSend() {

        var newPassword1 = document.getElementsByName('password')[0].value
        var newPassword2 = document.getElementsByName('password1')[0].value
        if (newPassword1 != newPassword2) {
            alert("Please input same passwords.");
            return false;
        }

        var username = document.getElementsByName('username')[0].value
        var email = document.getElementsByName('email')[0].value
        var account_type = 'pro_user'


        if (username == "" || email == "") {
            alert("please input username and email");
            return false;
        }
        if (newPassword1 == "") {
            alert("password error");
            return false;
        }

        $.post('<?php echo base_url() ?>user/createSpecial', {
            username: username,
            email: email,
            password: newPassword1,
            account_type: account_type
        }, function(data, status) {
            var obj = JSON.parse(data);
            if (obj.result == 'ok') {
                location.href = '<?php echo base_url() ?>/user/viewUserList';
            } else {
                alert(obj.message);
            }
        });


        return false;
    }
</script>
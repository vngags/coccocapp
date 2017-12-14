<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="LoginModal">
  <div class="modal-dialog maxw400" role="document">
    <div class="modal-content">
      <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <div class="login-area pl30 pr30">
              <h4 class="modal-title text-center">Đăng nhập</h4>
              <hr>
              <div class="modal-body-form">
                  <div class="form-group">
                      <a href="" class="btn btn-primary btn-block">Login Facebook</a>
                      <a href="" class="btn btn-primary btn-block">Login Google</a>
                  </div>
                  <hr>
                  <div class="form-group">
                      <label for="email">Email</label>
                      <input type="email" name="email" placeholder="Email" class="form-control">
                  </div>
                  <div class="form-group">
                      <label for="password">Mật khẩu</label>
                      <input type="password" name="password" placeholder="Mat khau" class="form-control">
                  </div>
                  <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                      <p class="text-center">Chưa có tài khoản? <strong><a href="{{ route('register') }}">Đăng ký</a></strong></p>
                  </div>
              </div>
          </div><!--Login area-->
          <div class="register-area" style="display:none">

          </div><!--div class="register-area"-->
      </div>
    </div>
  </div>
</div>

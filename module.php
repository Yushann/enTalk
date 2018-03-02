<?php
	function htmlHead( $m = 1 ){
		?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://getbootstrap.com/logo.png">
    <title>英文口說練習</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

    <!-- 日期時間 picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment-with-locales.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/locale/zh-tw.js"></script>
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <!-- 日期時間 picker -->

    <!-- firebase -->
    <script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
    <script src="https://www.gstatic.com/firebasejs/4.9.0/firebase-firestore.js"></script>
    <script src="js/utility.js"></script>
    <script src="js/model.js"></script>
    <script src="js/ui.js"></script>
    <script src="js/lang.tw.js"></script>
    <script src="https://cdn.firebase.com/libs/firebaseui/2.5.1/firebaseui.js"></script>
    <link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/2.5.1/firebaseui.css" />
    <!-- firebase -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
      <script async src="https://www.googletagmanager.com/gtag/js?id=UA-115000586-1"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-115000586-1');
      </script>



    <link href="css/blog.css" rel="stylesheet">
  </head>
  <body>

    <div class="container">
      <header class="py-3">
        <div class="row flex-nowrap justify-content-between align-items-center">
          <div class="col-6 pt-1">
            <a href="index.php"><h4 class="font-weight-bold text-dark">英文口說練習</h4></a>
          </div>
          <div class="col-4 d-flex justify-content-end">
            <a class="btn btn-sm btn-outline-info mr-2" href="javascript:CreateListUI.show();">新增</a>
            <a class="btn btn-sm btn-outline-secondary" id="loginTag" href="javascript:(Login.isLogin())?Login.logout():Login.login();">登入</a>
          </div>
        </div>
      </header>
      <p>
        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link active" href="javascript:;" onclick="Tab.showLists(this);">即將開始</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="javascript:;" onclick="Tab.showEndLists(this);">已結束</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="javascript:;" onclick="Tab.showMyLists(this);">參加過的</a>
          </li>
        </ul>
      </p>
		<?php
	}

  function cardTemplate(){
    ?>
      <script type="text/template" data-template="card_template">
        <div class="col-md-6" id="${list_id}">
          <span style="display: none;" id="${list_id}_character">${character}</span>
          <div class="card flex-md-row mb-4 box-shadow h-md-250">
            <div class="card-body d-flex flex-column align-items-start">
              <span style="width:100%">
                <small class="text-info border border-info float-right text-sm pl-1 pr-1" style="display: none">線上</small>
              </spans>
              <h3 class="mb-0">
                <a class="text-dark" href="https://getbootstrap.com/docs/4.0/examples/blog/#">${title}</a>
              </h3>
              <div class="mb-1 text-muted">${start_time}</div>
              <p class="card-text mb-auto">
                地點：${mode_data}<br>
                人數：${joined_number}<br>
                主持人：${host}<br>
                <span style="color:red">${display_character}</span><br>
              </p>
            <span style="width:100%"><a href="javascript:Card.join('${list_id}', '${join_btn_name}')" class="btn btn-${join_btn_class} float-right ${join_btn_disabled}" id="${list_id}_attend">${join_btn_name}</a></span>
            </div>
           
          </div>
        </div>
      </script>
    <?php
  }

	function htmlEnd(){
		?>
    </div>
      <footer class="blog-footer">
      </footer>
    </body>
  </html>
		<?php
	}

  function createListModal(){
    ?>
<div class="modal" id="createListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" >
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">新增聚會</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning" role="alert">
          google 暱稱將會顯示在頁面上
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">選擇集數</label>
          <div class="col-sm-9">
            <select class="form-control" id="seriesSelector" onchange="CreateListUI.selectSeries()">
            </select>
            <select class="form-control" id="seasonSelector" onchange="CreateListUI.selectSeason()">
            </select>
            <select class="form-control" id="episodeSelector" onchange="CreateListUI.selectEpisode()">
            </select>
          </div>
        </div>
        <div id="episodeContent">
        </div>
        <div class="form-group row">
          <label for="address" class="col-sm-3 col-form-label">地點</label>
          <div class="col-sm-9">
             <input type="email" class="form-control" id="address" placeholder="請輸入聚會地點">
          </div>
        </div>
        <div style="overflow:hidden;">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-8">
                        <div id="datetimepicker"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="CreateListUI.saveToFirestore()">新增</button>
      </div>
    </div>
  </div>
</div>
    <?php
  }

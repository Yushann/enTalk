<?php
  require_once('module.php');

  htmlHead();
?>
      <div class="row mb-2" id="card_container">
      </div>
<?php

  htmlEnd();
  loadingView();
  cardTemplate();
  history();
  createListModal();
?>
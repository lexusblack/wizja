  <canvas id="rotator"></canvas>
  <div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
      <div class="sidebar-nav">
        <ul>
          <li class="sidebar-brand">
            <a href="javascript:">
              <?= Yii::t('app', 'Space Planner') ?>
            </a>
          </li>
          <li class="sidebar-current-project">
            <!-- Nazwa projektu -->
          </li>
          <li class="menuView5">
            <a href="javascript:" class="open-project">
              <i class="fa fa-folder-open-o" aria-hidden="true"></i> <?= Yii::t('app', 'Otwórz projekt') ?>
            </a>
          </li>
          <li class="menuView5">
            <a href="javascript:" class="create-project new-project">
              <i class="fa fa-file-o" aria-hidden="true"></i> <?= Yii::t('app', 'Stwórz nowy projekt') ?>
            </a>
          </li>
          <li class="menuView5">
            <a href="/">
              <i class="fa fa-sign-out" aria-hidden="true"></i> <?= Yii::t('app', 'Powrót do systemu') ?>
            </a>
          </li>
          <li class="menuView1">
            <a href="javascript:" class="return-to-menuView5">
              <i class="fa fa-long-arrow-left" aria-hidden="true"></i> <?= Yii::t('app', 'Wróć') ?>
            </a>
          </li>
          <li class="menuView8">
            <a href="javascript:" class="return-to-menuView2">
              <i class="fa fa-long-arrow-left" aria-hidden="true"></i> <?= Yii::t('app', 'Wróć') ?>
            </a>
          </li>
          <li class="menuView1">
            <a href="javascript:" class="create-project normal">
              <i class="fa fa-window-maximize" aria-hidden="true"></i> <?= Yii::t('app', 'Dodaj salę') ?>
            </a>
          </li>
          <li class="menuView1">
            <a href="javascript:" class="create-project photo">
              <i class="fa fa-picture-o" aria-hidden="true"></i> <?= Yii::t('app', 'Dodaj plan sali') ?>
            </a>
          </li>
          <li class="menuView9">
            <a href="javascript:" class="change-room">
              <i class="fa fa-stop" aria-hidden="true"></i> <?= Yii::t('app', 'Zmień wymiary') ?>
            </a>
          </li>
          <li class="menuView10">
            <a href="javascript:" class="change-photo">
              <i class="fa fa-picture-o" aria-hidden="true"></i> <?= Yii::t('app', 'Zmień zdjęcie planu') ?>
            </a>
          </li>
          <li class="menuView2">
            <a href="javascript:" class="object-list">
              <i class="fa fa-object-group" aria-hidden="true"></i> <?= Yii::t('app', 'Umieść element') ?>
            </a>
          </li>
          <li class="menuView2">
            <a href="javascript:" class="send-mail">
              <i class="fa fa-envelope" aria-hidden="true"></i> <?= Yii::t('app', 'Wyślij email') ?>
            </a>
          </li>
          <li class="menuView2">
            <a href="javascript:" class="fetch-pdf">
              <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?= Yii::t('app', 'Pobierz pdf') ?>
            </a>
          </li>
          <li class="menuView2">
            <a href="javascript:" class="save-project">
              <i class="fa fa-floppy-o" aria-hidden="true"></i> <?= Yii::t('app', 'Zapisz zmiany') ?>
            </a>
          </li>
          <li class="menuView2">
            <a href="javascript:" class="close-project-with-save">
              <i class="fa fa-sign-out" aria-hidden="true"></i> <?= Yii::t('app', 'Zapisz i zamknij projekt') ?>
            </a>
          </li>
          <li class="menuView2">
            <a href="javascript:" class="close-project">
              <i class="fa fa-trash" aria-hidden="true"></i> <?= Yii::t('app', 'Zamknij projekt') ?>
            </a>
          </li>
          <li class="menuView3">
            <a href="javascript:" class="return-to-menuView2">
              <i class="fa fa-long-arrow-left" aria-hidden="true"></i> <?= Yii::t('app', 'Wróć') ?>
            </a>
          </li>

          <li class="menuView7">
            <a href="javascript:" class="return-to-menuView5">
              <i class="fa fa-long-arrow-left" aria-hidden="true"></i> <?= Yii::t('app', 'Wróć') ?>
            </a>
          </li>
        </ul>

        <div class="searchProject menuView6">
          <label for="finprojj"><i class="fa fa-search" aria-hidden="true"></i> <?= Yii::t('app', 'Wyszukaj projekt') ?></label>
          <div class="input-group">
            <input type="text" id="finprojj" class="form-control form-control-sm">
            <div class="input-group-btn">
              <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?= Yii::t('app', 'Sortuj') ?>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <a data-key="1" class="dropdown-item active" href="javascript:"><?= Yii::t('app', 'Od ostatnio edytowanego') ?></a>
                <a data-key="2" class="dropdown-item" href="javascript:"><?= Yii::t('app', 'Od najnowszego') ?></a>
                <a data-key="3" class="dropdown-item" href="javascript:"><?= Yii::t('app', 'Od najstarszego') ?></a>
                <a data-key="4" class="dropdown-item" href="javascript:"><?= Yii::t('app', 'Alfabetycznie A-Z') ?></a>
                <a data-key="5" class="dropdown-item" href="javascript:"><?= Yii::t('app', 'Alfabetycznie Z-A') ?></a>
              </div>
            </div>
          </div>
        </div>
        <div class="editobj menuView4">
          <div class="card">
            <div class="card-header">
                <?= Yii::t('app', 'Edycja elementu') ?>
            </div>
            <div class="card-body">
              <form>
                <div class="row">
                  <div class="col rect circles chairs tables">
                    <div class="form-group">
                      <label for="objname" class="col-form-label-sm"><?= Yii::t('app', 'Nazwa wyświetlana') ?></label>
                      <input maxlength="50" type="text" class="form-control form-control-sm" id="objname">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col circles">
                    <div class="form-group">
                      <label for="objfi" class="col-form-label-sm"><i class="fa fa-circle-o-notch" aria-hidden="true"></i> <?= Yii::t('app', 'Fi') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                      <input type="text" class="form-control form-control-sm numbr" id="objfi" value="0">
                    </div>
                  </div>
                  <div class="col circles">
                    <div class="form-group">
                      <label for="objheight" class="col-form-label-sm"><i class="fa fa-long-arrow-up" aria-hidden="true"></i> <?= Yii::t('app', 'Wys.') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                      <input type="text" class="form-control form-control-sm objheight numbr" value="0">
                    </div>
                  </div>
                  <div class="col col-4 tables">
                    <div class="form-group">
                      <label class="col-form-label-sm"><i class="fa fa-circle-o-notch" aria-hidden="true"></i> <?= Yii::t('app', 'Fi') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                      <div id="fis" class="btn-group" data-toggle="buttons">
                        <label data-value="160" class="btn btn-primary btn-sm active" onclick=blabla(this)>
                          <input type="radio" value="160" name="options" autocomplete="off" checked> 160
                        </label>
                        <label data-value="180" class="btn btn-primary btn-sm" onclick=blabla(this)>
                          <input type="radio" value="180" name="options" autocomplete="off"> 180
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col tables">
                    <div class="form-group">
                      <label class="col-form-label-sm"><?= Yii::t('app', 'Krzesła') ?> <small>(<?= Yii::t('app', 'szt') ?>)</small></label><br />
                      <div class="form-check form-check-inline">
                        <label class="custom-control custom-radio">
                          <input name="radio" type="radio" data-value="8" class="objchrs custom-control-input">
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">8</span>
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <label class="custom-control custom-radio">
                          <input id="radio2" name="radio" data-value="10" type="radio" class="objchrs custom-control-input">
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">10</span>
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <label class="custom-control custom-radio">
                          <input id="radio2" name="radio" data-value="12" type="radio" class="objchrs custom-control-input" disabled>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">12</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col rect chairs">
                    <div class="form-group">
                      <label for="objlength" class="col-form-label-sm"><i class="fa fa-arrows-h" aria-hidden="true"></i> <?= Yii::t('app', 'Dł.') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                      <input type="text" class="form-control form-control-sm numbr" id="objlength" value="0">
                    </div>
                  </div>
                  <div class="col rect chairs">
                    <div class="form-group">
                      <label for="objwidth" class="col-form-label-sm"><i class="fa fa-arrows-v" aria-hidden="true"></i> <?= Yii::t('app', 'Sz.') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                      <input type="text" class="form-control form-control-sm numbr" id="objwidth" value="0">
                    </div>
                  </div>
                  <div class="col rect chairs tables">
                    <div class="form-group">
                      <label for="objheight" class="col-form-label-sm"><i class="fa fa-long-arrow-up" aria-hidden="true"></i> <?= Yii::t('app', 'Wys.') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                      <input type="text" class="form-control form-control-sm numbr objheight" value="0">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col chairs tables circles">
                    <div class="form-group">
                      <label for="objrows" class="col-form-label-sm"><?= Yii::t('app', 'Ilość rzędów') ?></label>
                      <input type="text" class="form-control form-control-sm numbr" id="objrows" value="0">
                    </div>
                  </div>
                  <div class="col chairs tables circles">
                    <div class="form-group">
                      <label for="objperrow" class="col-form-label-sm"><?= Yii::t('app', 'Ilość w rzędzie') ?></label>
                      <input type="text" class="form-control form-control-sm numbr" id="objperrow" value="0">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col chairs tables circles">
                    <div class="form-group">
                      <label for="objaisle" class="col-form-label-sm"> <?= Yii::t('app', 'Odstęp rzędów') ?></label>
                      <input type="text" class="form-control form-control-sm numbr" id="objaisle" value="0">
                    </div>
                  </div>
                  <div class="col chairs tables circles">
                    <div class="form-group">
                      <label for="objaisle" class="col-form-label-sm"> <?= Yii::t('app', 'Odstęp w rzędzie') ?></label>
                      <input type="text" class="form-control form-control-sm numbr" id="objbetween" value="0">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col rect circles chairs tables">
                    <div class="form-group">
                      <label for="objcolor" class="col-form-label-sm"><?= Yii::t('app', 'Kolor') ?></label>
                      <input class="form-control" type="color" value="#FFFFFF" id="objcolor">
                    </div>
                  </div>
                  <div class="col rect chairs tables circles">
                    <div class="form-group">
                      <label for="objradius" class="col-form-label-sm"><?= Yii::t('app', 'Obrót') ?></label>
                      <div class="input-group">
                        <input type="text" class="form-control numbr form-control-sm" id="objradius" value="0">
                        <span class="input-group-btn">
                          <button id="objrotateleft" class="btn btn-light btn-sm" type="button"><i class="fa fa-rotate-left" aria-hidden="true"></i></button>
                        </span>
                        <span class="input-group-btn">
                          <button id="objrotateright" class="btn btn-light btn-sm" type="button"><i class="fa fa-rotate-right" aria-hidden="true"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col circles rect chairs tables">
                    <div class="form-group">
                      <label class="col-form-label-sm"><?= Yii::t('app', 'Warstwa') ?></label>
                      <div class="input-group input-group-sm">
                        <span class="input-group-addon" id="basic-addon3"><?= Yii::t('app', 'Przesuń na') ?> </span>
                        <span class="input-group-btn">
                          <button id="objlaydown" type="button" class="btn btn-light btn-sm"><i class="fa fa-level-down" aria-hidden="true"></i></button>
                        </span>
                        <span class="input-group-btn">
                          <button id="objlayup" type="button" class="btn btn-light btn-sm"><i class="fa fa-level-up" aria-hidden="true"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col rect circles chairs tables">
                    <div class="form-group">
                      <label class="col-form-label-sm"><?= Yii::t('app', 'Strefa') ?></label>
                      <input class="form-control form-control-sm numbr" type="text" id="objsafe" />
                    </div>
                  </div>
                </div>
                <div class="form-check rect circles chairs tables" style="margin-bottom: 1rem;">
                  <label class="form-check-label">
                    <input id="objshowdims" type="checkbox" class="form-check-input">
                      <?= Yii::t('app', 'Pokaż wymiary') ?>
                  </label>
                </div>
                <div class="row">
                  <div class="col rect circles chairs tables">
                    <div class="form-group">
                      <input id="objremove" type="button" class="btn btn-danger btn-sm" value="<?= Yii::t('app', 'Usuń') ?>">
                      <input id="objcopy" type="button" class="btn btn-light btn-sm" value="<?= Yii::t('app', 'Kopiuj') ?>">
                      <input type="button" class="return-to-menuView2 cancel-objedit btn btn-light btn-sm" value="<?= Yii::t('app', 'Zapisz') ?>">
                      <input id="objreset" type="button" class="return-to-menuView2 cancel-objedit btn btn-warning btn-sm" value="<?= Yii::t('app', 'Anuluj') ?>">
                    </div>
                  </div>
                </div>

                <!-- <input id="objcopy" type="button" class="btn btn-warning btn-sm" value="Duplikuj"> -->
              </form>
            </div>
          </div>

        </div>
        <div class="editobj menuView7">
          <div class="card">
            <div class="card-header">
                <?= Yii::t('app', 'Edycja projektu') ?>
            </div>
            <div class="card-body">
              <form>
                <input type="hidden" id="projid" />
                <div class="form-group">
                  <label for="projname" class="col-form-label-sm"><?= Yii::t('app', 'Nazwa wyświetlana') ?></label>
                  <input maxlength="30" type="text" class="form-control form-control-sm projname" id="projname">
                </div>
                <div class="form-group">
                  <label for="projdesc" class="col-form-label-sm"><?= Yii::t('app', 'Opis') ?></label>
                  <textarea maxlength="300" id="projdesc" class="form-control form-control-sm" rows="3"></textarea>
                </div>
                <div class="form-group">
                  <input id="projsavechange" type="button" class="btn btn-light btn-sm" value="<?= Yii::t('app', 'Zapisz') ?>">
                </div>
              </form>
            </div>
          </div>

        </div>

        <div class="editpom menuView8">
          <div class="card">
            <div class="card-header">
                <?= Yii::t('app', 'Edycja pomieszczenia') ?>
            </div>
            <div class="card-body">
              <form>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="pomlength" class="col-form-label-sm"><i class="fa fa-arrows-h" aria-hidden="true"></i> <?= Yii::t('app', 'Długość') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></label>
                      <input type="text" class="form-control numbr form-control-sm" id="pomlength">
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="pomwidth" class="col-form-label-sm"><i class="fa fa-arrows-v" aria-hidden="true"></i> <?= Yii::t('app', 'Szerokość') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></label>
                      <input type="text" class="form-control numbr form-control-sm" id="pomwidth">
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="pomheight" class="col-form-label-sm"><i class="fa fa-long-arrow-up" aria-hidden="true"></i> <?= Yii::t('app', 'Wysokość') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></label>
                      <input type="text" class="form-control numbr form-control-sm" id="pomheight">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <input id="pomsavechange" type="button" class="btn btn-light btn-sm" value="<?= Yii::t('app', 'Zapisz') ?>">
                </div>
              </form>
            </div>
          </div>

        </div>
        <div class="listo projectlist menuView6">
          <div class="list-group">
            <!-- Lista projektów -->
          </div>
        </div>
        <div class="listo itemlist menuView3">
          <h5 class="hdr"><?= Yii::t('app', 'Przeciągnij i upuść na plan') ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></h5>
          <div class="list-group">
            <!-- lista elementów -->
          </div>
        </div>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
      <div class="biglogo">
        <span class="animated flash"><?= Yii::t('app', 'Space Planner') ?></span>
      </div>

      <nav id="toolbar" class="nav nav-pills nav-justified">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle btn-primary btn-sm disabled" data-toggle="dropdown" href="javascript:" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-toggle-on" aria-hidden="true"></i> <?= Yii::t('app', 'Wymiary') ?></a>
          <div class="dropdown-menu">
            <a class="dropdown-item" id="showAllDims" href="javascript:" role="tab" data-toggle="pill" aria-controls="pills-dropdown1"><?= Yii::t('app', 'Pokaż wymiary elementów') ?></a>
            <a class="dropdown-item" id="hideAllDims" href="javascript:" role="tab" data-toggle="pill" aria-controls="pills-dropdown2"><?= Yii::t('app', 'Ukryj wymiary elementów') ?></a>
          </div>
        </li>
      </nav>

      <div id="addworldhere">
        <div id="enterDimensions" class="modal fade">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app', 'Dodaj salę') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label for="roomLength"><i class="fa fa-arrows-h" aria-hidden="true"></i> <?= Yii::t('app', 'Dł.') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="roomLength" placeholder="<?= Yii::t('app', 'np.') ?> 12">
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label for="roomWidth"><i class="fa fa-arrows-v" aria-hidden="true"></i> <?= Yii::t('app', 'Sz.') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="roomWidth" placeholder="<?= Yii::t('app', 'np.') ?> 20">
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label for="roomHeight"><i class="fa fa-arrows-h" aria-hidden="true"></i> <?= Yii::t('app', 'Wy.') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="roomHeight" placeholder="<?= Yii::t('app', 'np.') ?> 5">
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button id="create-normal-accept" type="button" class="btn btn-primary"><?= Yii::t('app', 'Dodaj') ?></button>
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app', 'Anuluj') ?></button>
              </div>
            </div>
          </div>
        </div>

        <div id="projectadd" class="modal fade">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app', 'Stwórz nowy projekt') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form>
                  <div class="form-group">
                    <label for="projName"><?= Yii::t('app', 'Nazwa projektu') ?></label>
                    <input maxlength="30" type="text" class="form-control" id="projName" placeholder="<?= Yii::t('app', 'np. Konferencja Warszawa') ?>">
                  </div>
                  <div class="form-group">
                    <label for="projDesc"><?= Yii::t('app', 'Opis') ?></label>
                    <textarea maxlength="300" id="projDesc" class="form-control" rows="3"></textarea>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button id="projectaddaccept" type="button" class="btn btn-primary"><?= Yii::t('app', 'Stwórz') ?></button>
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app', 'Anuluj') ?></button>
              </div>
            </div>
          </div>
        </div>

        <div id="objadd" class="modal fade" tabindex="-1">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app', 'Dodaj element') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                  <h6><?= Yii::t('app', 'Wykryto następujące błędy') ?>:</h6>
                  <ol>
                    <!-- Błędy -->
                  </ol>
                </div>
                <form>
                  <input type="hidden" id="objTop" />
                  <input type="hidden" id="objLeft" />
                  <input type="hidden" id="objShape" />
                  <input type="hidden" id="objForceShowHeight" />
                  <div class="row">
                    <div class="col rect circles chairs tables">
                      <div class="form-group">
                        <label for="objName"><?= Yii::t('app', 'Nazwa wyświetlana') ?></label>
                        <input maxlength="50" type="text" class="form-control" id="objName">
                      </div>
                    </div>
                    <div class="col rect circles chairs">
                      <div class="form-group">
                        <label for="objColor" class="col-form-label="><?= Yii::t('app', 'Kolor') ?></label>
                        <input class="form-control" type="color" value="#FFFFFF" id="objColor">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col rect chairs">
                      <div class="form-group">
                        <label for="objLength"><i class="fa fa-arrows-h" aria-hidden="true"></i> <?= Yii::t('app', 'Dł.') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="objLength">
                      </div>
                    </div>
                    <div class="col rect chairs">
                      <div class="form-group">
                        <label for="objWidth"><i class="fa fa-arrows-v" aria-hidden="true"></i> <?= Yii::t('app', 'Sz.') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="objWidth">
                      </div>
                    </div>
                    <div class="col circles">
                      <div class="form-group">
                        <label for="objFi"><i class="fa fa-circle-o-notch" aria-hidden="true"></i> <?= Yii::t('app', 'Fi') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="objFi">
                      </div>
                    </div>
                    <div class="col col-4 tables">
                      <div class="form-group">
                        <label><i class="fa fa-circle-o-notch" aria-hidden="true"></i> <?= Yii::t('app', 'Fi') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label><br />
                        <div id="fis" class="btn-group" data-toggle="buttons">
                          <label data-value="160" class="btn btn-primary" onclick=blabla(this)>
                            <input type="radio" value="160" name="options" autocomplete="off"> 160
                          </label>
                          <label data-value="180" class="btn btn-primary" onclick=blabla(this)>
                            <input type="radio" value="180" name="options" autocomplete="off"> 180
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col col-5 tables">
                      <div class="form-group">
                        <label class="col-form-label-sm"><?= Yii::t('app', 'Krzesła') ?> <small>(<?= Yii::t('app', 'szt') ?>)</small></label><br />
                        <div class="form-check form-check-inline">
                          <label class="custom-control custom-radio">
                            <input name="radio" type="radio" data-value="8" class="objchrs custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">8</span>
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="custom-control custom-radio">
                            <input id="radio2" name="radio" data-value="10" type="radio" class="objchrs custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">10</span>
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="custom-control custom-radio">
                            <input id="radio2" name="radio" data-value="12" type="radio" class="objchrs custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">12</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col rect circles chairs tables">
                      <div class="form-group">
                        <label for="objHeight"><i class="fa fa-long-arrow-up" aria-hidden="true"></i> <?= Yii::t('app', 'Wys.') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="objHeight">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col chairs tables circles">
                      <div class="form-group">
                        <label for="objRows"><i class="fa fa-bars" aria-hidden="true"></i> <?= Yii::t('app', 'Ilość rzędów') ?></label>
                        <input type="text" class="form-control numbr" id="objRows">
                      </div>
                    </div>
                    <div class="col chairs">
                      <div class="form-group">
                        <label for="objPerRow"><i class="fa fa-th" aria-hidden="true"></i> <?= Yii::t('app', 'Ilość krzeseł w rzędzie') ?></label>
                        <input type="text" class="form-control numbr" id="objPerRow">
                      </div>
                    </div>
                    <div class="col tables circles">
                      <div class="form-group">
                        <label for="objPerRow"><i class="fa fa-th" aria-hidden="true"></i> <?= Yii::t('app', 'Ilość stołów w rzędzie') ?></label>
                        <input type="text" class="form-control numbr" id="objPerRow">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col chairs tables circles">
                      <div class="form-group">
                        <label for="objAisle"><i class="fa fa-arrows-v" aria-hidden="true"></i> <?= Yii::t('app', 'Odstęp rzędów') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="objAisle">
                      </div>
                    </div>
                    <div class="col chairs tables circles">
                      <div class="form-group">
                        <label for="objBetween"><i class="fa fa-exchange" aria-hidden="true"></i> <?= Yii::t('app', 'Odstęp krzeseł') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                        <input type="text" class="form-control numbr" id="objBetween">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col rect circles chairs tables">
                      <div class="form-group">
                        <label for="objSafe"><?= Yii::t('app', 'Bezpieczna strefa') ?> <small>(<?= Yii::t('app', 'cm') ?>)</small></label>
                        <input class="form-control numbr" type="text" id="objSafe" />
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button id="objaddaccept" type="button" class="btn btn-primary"><?= Yii::t('app', 'Dodaj') ?></button>
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app', 'Anuluj') ?></button>
              </div>
            </div>
          </div>
        </div>

        <div id="cropphoto" class="modal fade" tabindex="-1">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app', 'Dodaj plan sali') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="alert alert-light" role="alert">
                  <h4 class="alert-heading"><?= Yii::t('app', 'Skalowanie zdjęcia') ?></h4>
                  <p class="mb-0"><?= Yii::t('app', 'Wybierz dowolny obszar na planie i wpisz jego rzeczywistą wysokość, długość lub/i szerokość.') ?></p>
                  <p>
                    <b><?= Yii::t('app', 'Jeśli znasz tylko długość lub szerokość możesz zostawić drugi wymiar nieuzupełniony.') ?></b>
                  </p>
                </div>
                <img id="roomphoto" alt="photo">
              </div>
              <div class="modal-footer">
                <form class="form-inline">
                  <div class="row">
                    <div class="col-9">
                      <div class="row">
                        <div class="col-4">
                          <div class="input-group">
                            <input id="cropLength" type="text" class="form-control numbr" required>
                            <span class="input-group-addon"><span class="qwe"><i class="fa fa-arrows-h" aria-hidden="true"></i> <?= Yii::t('app', 'Dł.') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></span></span>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="input-group">
                            <input id="cropWidth" type="text" class="form-control numbr" required>
                            <span class="input-group-addon"><span class="qwe"><i class="fa fa-arrows-v" aria-hidden="true"></i> <?= Yii::t('app', 'Sz.') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></span></span>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="input-group">
                            <input id="cropHeight" type="text" class="form-control numbr" required>
                            <span class="input-group-addon"><span class="qwe"><i class="fa fa-long-arrow-up" aria-hidden="true"></i> <?= Yii::t('app', 'Wy.') ?> <small>(<?= Yii::t('app', 'm') ?>)</small></span></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-3">
                      <input id="cropphotoaccept" type="submit" class="btn btn-primary" value="<?= Yii::t('app', 'Dodaj') ?>">
                      <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app', 'Anuluj') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div id="sendemail" class="modal" tabindex="-1">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app', 'Wyślij email') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form>
                  <div class="form-group">
                    <label for="emailo"><?= Yii::t('app', 'Adres email') ?></label>
                    <input maxlength="100" type="email" class="form-control" id="emailo" placeholder="name@example.com">
                  </div>
                  <div class="form-group">
                    <label maxlength="100" for="topico"><?= Yii::t('app', 'Temat') ?></label>
                    <input type="text" class="form-control" id="topico">
                  </div>
                  <div class="form-group">
                    <label for="descro"><?= Yii::t('app', 'Wiadomość') ?></label>
                    <textarea class="form-control" id="descro" rows="3"></textarea>
                  </div>
                  <div class="form-group">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" id="ispdfo" checked> <?= Yii::t('app', 'Dołącz PDF') ?>
                      </label>
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="acceptsendemail"><?= Yii::t('app', 'Wyślij') ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('app', 'Anuluj') ?></button>
              </div>
            </div>
          </div>
        </div>

        <div class="ctrls">
          <button type="button" class="map-up btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Przesuń widok do góry.') ?>"><i class="fa fa-arrow-up" aria-hidden="true"></i></button>
          <button type="button" class="map-down btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Przesuń widok w dół.') ?>"><i class="fa fa-arrow-down" aria-hidden="true"></i></button>
          <button type="button" class="map-left btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Przesuń widok w lewo.') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
          <button type="button" class="map-right btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Przesuń widok w prawo.') ?>"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
          <button type="button" class="map-plus btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Powiększ widok.') ?>"><i class="fa fa-plus" aria-hidden="true"></i></button>
          <button type="button" class="map-minus btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Pomniejsz widok.') ?>"><i class="fa fa-minus" aria-hidden="true"></i></button>
          <button type="button" class="map-measure btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Miarka. Przytrzymaj SHIFT, aby narysować prostą linię.') ?>"><i class="fa fa-thumb-tack" aria-hidden="true"></i></button>
          <button type="button" class="map-line btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Zaznacz wymiar. Przytrzymaj SHIFT, aby narysować prostą linię.') ?>"><i class="fa fa-thumb-tack" aria-hidden="true"></i></button>
          <button type="button" class="map-adjust btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="top" title="<?= Yii::t('app', 'Wyśrodkuj widok.') ?>"><i class="fa fa-compress" aria-hidden="true"></i></button>
          <button type="button" class="map-pdf btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Zapisz jako pdf.') ?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
          <button type="button" class="map-mail btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Wyślij email') ?>"><i class="fa fa-envelope" aria-hidden="true"></i></button>
          <button type="button" class="map-block btn btn-sm" data-trigger="hover" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Przytwierdź planszę') ?>"><i class="fa fa-anchor" aria-hidden="true"></i></button>
        </div>
        <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">
          <i class="fa fa-bars" aria-hidden="true"></i>
        </a>
      </div>
    </div>
  </div>
<script type="text/javascript">
var saveUrl='/admin/spaceplanner/save';
var updateUrl='/admin/spaceplanner/update';
var indexUrl='/admin/spaceplanner/all';
var deleteUrl='/admin/spaceplanner/delete';
var loadUrl='/admin/spaceplanner/load';
var emailUrl='/admin/spaceplanner/email';
var logo1Url ='<?=\Yii::getAlias('@uploads' . '/settings/').$logo?>';
var logo2Url='/themes/e4e/img/newem.png';
</script>
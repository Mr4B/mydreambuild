<?php 
// Barra di navigazione
class NavBar {
    private $logedIn = false;
    private $role = 4;
    private $username;
    
    public function __construct() {
        // $this->logedIn = $logedIn;
    }
    
    public function setLogin($username, $role) {
        $this->logedIn = true;
        $this->username = $username;
        $this->role = $role;
    }

    public function getLogin() {
        return $this->logedIn;
    }

    public function Logout() {
        $this->logedIn = false;
    }

    public function setRole($role) {
        $this -> role = $role;
    }
    
    public function getNavBar() {
      $html = 
          "
          <div class='jumbotron jumbotron-fluid mb-3 mt-2'>
            <div class='container'>  
                <img src='../img/logo.png' alt='logo' width=70 class='d-inline-block align-top' style='float: left; margin-right: 30px;'>
                <h1 class='display-4'>MYDREAMBUILD</h1>
            </div>
          </div>

          <nav class='navbar navbar-expand-lg navbar-dark bg-success mb-3'>
            <div class='container-fluid'>
              <a class='nav-link navbar-brand' href='../main/home.php'>Home</a>
              <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#nav-content' aria-controls='nav-content' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
              </button>
              
              <div class='collapse navbar-collapse' id='nav-content'>
                <ul class='navbar-nav me-auto'>
                <li class='nav-item'>
                  <a class='nav-link navbar-brand' href='../configurations/configurazione.php'>Configurazioni</a>
                </li>";

        if ($this->role <= 2) {
          $html .= "
                    <li class='nav-item dropdown'>
                      <a class='nav-link navbar-brand dropdown-toggle' href='#' id='gestione' data-bs-toggle='dropdown' aria-expanded='false'>Gestione sito</a>
                      <ul class='dropdown-menu dropdown-menu-start' aria-labelledby='gestione'>
                        <li><a class='dropdown-item' href='../product/gestione_prodotti.php'>Prodotti</a></li>
                        <li><a class='dropdown-item' href='../articles/gestione_articoli.php'>Articoli</a></li>
                        <li><a class='dropdown-item' href='../configurations/gestione_configurazioni.php'>Configurazioni</a></li>
                      </ul>
                    </li>";
        }

        $html .= "
                </ul>
                <ul class='navbar-nav ms-auto'>";

        if ($this->getLogin() === true) {
          $html .= "
                    <li class='nav-item dropdown'>
                      <a class='nav-link navbar-brand dropdown-toggle' href='#' id='profilo' data-bs-toggle='dropdown' aria-expanded='false'>
                        <img src='../img/login.png' alt='Omino stilizzato' height='30' width='30'>
                      </a>
                      <ul class='dropdown-menu dropdown-menu-end' aria-labelledby='profilo'>
                        <li><a class='dropdown-item' href='../user/profilo.php'>Profilo</a></li>
                        <li><a class='dropdown-item' href='../user/logout.php'>Logout</a></li>
                      </ul>
                    </li>";
        } else {
          $html .= "
                    <li class='nav-item'>
                      <a class='nav-link navbar-brand' href='../user/login.php'>Accedi</a>
                    </li>";
        }

        $html .= "
                </ul>
              </div>
            </div>
          </nav>";


      return $html;
    }
}
?>
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
      $html = "
          <style>
              /* Stile per il jumbotron */
              .jumbotron {
                  background-color: #bebebe; /* Colore di sfondo leggermente grigio */
                  color: #343a40; /* Colore del testo scuro */
                  padding: 1.3rem 1rem; /* Aumenta il padding per una migliore spaziatura */
                  border-radius: 0.5rem; /* Arrotonda gli angoli */
                  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Aggiungi un'ombra */
              }
              .jumbotron h1 {
                  font-size: 2.5rem; /* Dimensione del font del titolo */
                  font-weight: bold; /* Grassetto per il titolo */
              }
              .jumbotron p {
                  font-size: 1.25rem; /* Dimensione del font del paragrafo */
              }
              /* Stile per la barra di navigazione */
              .navbar {
                  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Aggiungi un'ombra alla barra di navigazione */
              }
              .navbar .navbar-brand,
              .navbar .nav-link {
                  color: #ffffff !important; /* Colore del testo bianco */
                  font-size: 1.2rem; /* Aumenta leggermente la dimensione del font */
                  font-weight: 500; /* Semi-grassetto */
                  margin-left: 2rem;
              }
              .navbar .nav-link:hover {
                  color: #d4d4d4 !important; /* Colore del testo al passaggio del mouse */
              }
              .navbar .dropdown-menu {
                  background-color: #343a40; /* Colore di sfondo del menu a tendina */
                  border: none; /* Rimuovi il bordo */
              }
              .navbar .dropdown-menu .dropdown-item {
                  color: #ffffff; /* Colore del testo del menu a tendina */
              }
              .navbar .dropdown-menu .dropdown-item:hover {
                  background-color: #495057; /* Colore di sfondo al passaggio del mouse */
              }
              .navbar .dropdown-toggle::after {
                  border-top-color: #ffffff; /* Colore della freccia del menu a tendina */
              }
          </style>

          <div class='jumbotron jumbotron-fluid mb-3 mt-2 bg-light text-dark'>
            <div class='container'>  
                <img src='../img/logo.png' alt='logo' width=95 class='d-inline-block align-top' style='float: left; margin-right: 20px;'>
                <h1 class='display-4'>MYDREAMBUILD</h1>
                <p class='lead'>La tua piattaforma di configurazione PC</p>
            </div>
          </div>

          <nav class='navbar navbar-expand-lg navbar-dark bg-success mb-3'>
            <div class='container-fluid'>
              <a class='navbar-brand' href='../main/home.php'>Home</a>
              <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#nav-content' aria-controls='nav-content' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
              </button>
              
              <div class='collapse navbar-collapse' id='nav-content'>
                <ul class='navbar-nav me-auto'>
                  <li class='nav-item'>
                    <a class='nav-link' href='../articles/articoli.php'>Articoli</a>
                  </li>
                  <li class='nav-item'>
                    <a class='nav-link' href='../configurations/configurazione.php'>Configurazioni</a>
                  </li>
                  ";

        if ($this->role <= 2) {
          $html .= "
                    <li class='nav-item dropdown'>
                      <a class='nav-link dropdown-toggle' href='#' id='gestione' data-bs-toggle='dropdown' aria-expanded='false'>Gestione sito</a>
                      <ul class='dropdown-menu' aria-labelledby='gestione'>
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
                      <a class='nav-link dropdown-toggle' href='#' id='profilo' data-bs-toggle='dropdown' aria-expanded='false'>
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
                      <a class='nav-link' href='../user/login.php'>Accedi</a>
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

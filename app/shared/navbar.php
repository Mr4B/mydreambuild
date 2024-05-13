<!--Barra di navigazione-->
<?php 

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
        "<nav class='navbar navbar-expand-lg navbar-light bg-secondary mb-3'> <!--mb-[x] = margin-bottom-->
        <div class='container-fluid'>
            <!--Burger-->
            <button class='navbar-toggler' type='button' 
            data-toggle='collapse' data-target='#nav-content'
            aria-controls='nav-content' aria-expanded='false'
            aria-label='Toggle navigation'>
            <span class='navbar-toggler-icon'></span>
            </button>
        
            <div class='collapse navbar-collapse' id='nav-content'>
                <ul class='navbar-nav mx-auto'>
                    <li class='nav-item mr-sm-4'>
                        <a class='nav-link' href='../main/home.php' title='Home'>Home</a>
                    </li>

                    <!-- esempio dropdown // COMMENTATO
                    <li class='nav-item dropdown mr-sm-4'>
                        <a class='nav-link dropdown-toggle' href='#' title='Contatti' id='utili' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Servizi</a>
                        <div class='dropdown-menu' aria-labelledby='utili'>
                            <a class='dropdown-item' href='utili/link/link.html'>Link utili</a>
                            <a class='dropdown-item' href='utili/info/info.html'>Info turistiche</a>
                        </div>
                    </li> -->
                    ";
        if($this->role <= 2) {
            $html.= "
                    <li class='nav-item mr-sm-4'>
                        <a class='nav-link' href='../product/gestione_prodotti.php'>Gestione prodotti</a>
                    </li>
                    ";
        }
        // Gestione profilo
        if($this->getLogin() === true) {
            $html .= "               
                    <li class='nav-item dropdown mr-sm-4'>
                        <a class='nav-link dropdown-toggle' href='#' title='Contatti' id='utili' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><img src='../img/login.png' alt='Omino stilizzato' height=30 width=30></a>
                        <div class='dropdown-menu' aria-labelledby='utili'>
                            <a class='dropdown-item' href='../user/profilo.php' title='Profile'>Profilo</a>
                            <a class='dropdown-item' href='../user/logout.php'>Logout</a>
                        </div>
                    </li> 
                    ";
        } else {
            $html .="<li class='nav-item mr-sm-4'>
                    <a class='nav-link' href='../user/login.php' title='Accedi'>Accedi</a>
                </li>    ";
        }
        
        $html .= "
                    </ul>
            
                    <!--Cerca - COMMENTATO-->
                    <!-- <form class='d-flex ms-auto'>
                        <div class='input-group'>
                        <button class='btn btn-dark' type='submit'>
                        <span class='material-icons'>search</span>
                        </button>
                        </div>
                    </form> -->
        
            </div>
        </div>
        </nav>";

        return $html;
    }
}


/* function getNavBar() { /* passargli come argomento i link della navbar, poi riusarli come variabili *

$html = "<nav class='navbar navbar-expand-lg navbar-light bg-secondary mb-3'> <!--mb-[x] = margin-bottom-->
    <div class='container'>
        <!--Burger-->
        <button class='navbar-toggler' type='button' 
        data-toggle='collapse' data-target='#nav-content'
        aria-controls='nav-content' aria-expanded='false'
        aria-label='Toggle navigation'>
            <span class='navbar-toggler-icon'></span>
        </button>
        
        <div class='collapse navbar-collapse' id='nav-content'>
            <ul class='navbar-nav mx-auto'>
                <li class='nav-item mr-sm-4'>
                    <a class='nav-link' href='' title='Home'>Home</a>
                </li>
                <li class='nav-item mr-sm-4'>
                    <a class='nav-link' href='' title='Clienti'>Clienti</a>
                </li>
                <li class='nav-item mr-sm-4'>
                    <a class='nav-link' href='' title='Tipo Intervento'>Interventi disponibili</a>
                </li>
                <li class='nav-item mr-sm-4'>
                    <a class='nav-link' href='amministrazione/amministrazione.html' title='Comune e amministrazione'>Comune e amministrazione</a>
                </li>

                <!-- esempio dropdown 
                <li class='nav-item dropdown mr-sm-4'>
                    <a class='nav-link dropdown-toggle' href='#' title='Contatti' id='utili' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Servizi</a>
                    <div class='dropdown-menu' aria-labelledby='utili'>
                        <a class='dropdown-item' href='utili/link/link.html'>Link utili</a>
                        <a class='dropdown-item' href='utili/info/info.html'>Info turistiche</a>
                    </div>
                </li> -->
            </ul>

            <!--Cerca-->
            <!-- <form class='d-flex ms-auto'>
                <div class='input-group'>
                    <button class='btn btn-dark' type='submit'>
                        <span class='material-icons'>search</span>
                    </button>
                </div>
            </form> -->

        </div>
    </div>
</nav>";

return $html;
}
 */
?>
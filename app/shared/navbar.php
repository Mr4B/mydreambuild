<!--Barra di navigazione-->
<?php 
function getNavBar() { /* passargli come argomento i link della navbar, poi riusarli come variabili */

$html = "<nav class='navbar navbar-expand-lg navbar-dark bg-dark mb-3'> <!--mb-[x] = margin-bottom-->
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
?>
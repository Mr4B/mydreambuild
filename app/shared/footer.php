<?php
class Footer {
    public function getFooter() {
        $html = "
        <style>
            .footer {
                color: #ffffff; /* Colore del testo bianco */
                padding: 2rem 1rem;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-wrap: wrap;
            }
            .footer .social-icons, .footer .logo, .footer .contact {
                display: flex;
                align-items: center;
            }
            .footer .social-icons img {
                height: 30px;
                width: 30px;
                margin-right: 18px
            }
            .footer .contact img {
                height: 25px;
                width: 35px;
                margin-right: 15px;
            }
            .footer .social-icons a, .footer .contact a {
                color: #ffffff;
                text-decoration: none;
                margin-right: 15px;
            }
            .footer .logo {
                flex-grow: 1; /* Permette alla colonna centrale di espandersi */
                display: flex;
                justify-content: center; /* Centra il contenuto all'interno della colonna */
            }
            .footer .logo img {
                height: 90px; /* Altezza del logo */
            }
            .footer .contact a {
                display: flex;
                align-items: right; 
                text-align: right;
            }
            .footer .contact a img {
                margin-right: 10px; /* Spaziatura tra l'icona e il testo */
            }
            .footer .contact {
                padding-left: 10%; /* Spaziatura tra l'icona e il testo */
            }
        </style>

        <footer class='footer bg-success'>
            <div class='container'>
                <div class='row w-100'>
                    <div class='col-md-4 col-12 social-icons text-md-start text-center mb-2 mb-md-0'>
                        <a href='https://t.me/tuo_profiliotelegram' target='_blank'>
                            <img src='../img/telegram.png' alt='Telegram'>
                        </a>
                        <a href='https://instagram.com/tuo_profiloinstagram' target='_blank'>
                            <img src='../img/instagram.png' alt='Instagram'>
                        </a>
                        <a href='https://facebook.com/tuo_profilofacebook' target='_blank'>
                            <img src='../img/facebook.png' alt='Facebook'>
                        </a>
                    </div>
                    <div class='col-md-4 col-12 logo mb-2 mb-md-0'>
                        <img src='../img/logo.png' alt='Logo'>
                    </div>
                    <div class='col-md-4 col-12 contact text-md-end text-center'>
                        <a href='../user/segnala_problema.php'>
                            <img src='../img/mail.png' alt='Mail'>
                            <span>Segnala un problema</span>
                        </a>
                    </div>
                </div>
            </div>
        </footer>";
        return $html;
    }
}
?>

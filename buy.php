
<?php
// Connect to webserver
session_start();
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'kkzrt';
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kapcsolódási hiba: " . $e->getMessage());
}
//Html Start 
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Számla Generátor</title>
    <link rel="stylesheet" href="buy.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="betölt.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="nav-wrapper">
            <div class="nav-container">
                    <button class="menu-btn" id="menuBtn">
                        <div class="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>
                <nav class="dropdown-menu" id="dropdownMenu">
                    <ul class="menu-items">
                        <li>
                            <a href="index.php" class="active">
                                <img src="placeholder.png" alt="Főoldal">
                                <span>Főoldal</span>
                            </a>
                        </li>
                        <li>
                            <a href="buy.php">
                                <img src="tickets.png" alt="Jegyvásárlás">
                                <span>Jegyvásárlás</span>
                            </a>
                        </li>
                        <li>
                            <a href="menetrend.php">
                                <img src="calendar.png" alt="Menetrend">
                                <span>Menetrend</span>
                            </a>
                        </li>
                        <li>
                            <a href="jaratok.php">
                                <img src="bus.png" alt="járatok">
                                <span>Járatok</span>
                            </a>
                        </li>
                        <li>
                            <a href="info.php">
                                <img src="information-button.png" alt="Információ">
                                <span>Információ</span>
                            </a>
                        </li>
                        <li>
                            <a href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Kijelentkezés</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
             <div id="toggle"></div><br>
            <h1><i class="fas fa-map-marked-alt"></i> Jegy és bérlet vásárlás</h1>
        </div>
            </ul>
            </button>
          </nav>
        <div class="navh1">
        </div>
    </div>
    <div style="margin-top: 5%;" class="container">
        <form id="invoiceForm" novalidate>
            <div style="font-weight: bold;" class="section-title">Vásárló adatai</div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Teljes név*</label>
                    <input type="text" id="name" pattern="[A-Za-zÀ-ž\s]{2,50}" 
                           placeholder="pl. Nagy János" required>
                    <div class="error-message">Kérjük, adjon meg egy érvényes nevet (2-50 karakter)</div>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">E-mail cím*</label>
                    <input type="email" id="email" 
                           placeholder="pelda@email.hu" required>
                    <div class="error-message">Kérjük, adjon meg egy érvényes email címet</div>
                </div>
            </div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Telefonszám*</label>
                    <input type="tel" id="phone" 
                           placeholder="+36 30 123 4567" required>
                    <div class="error-message">Kérjük, adjon meg egy érvényes telefonszámot</div>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Adószám</label>
                    <input type="text" id="vatNumber" 
                           pattern="[0-9]{8}-[0-9]{1}-[0-9]{2}"
                           placeholder="12345678-1-12">
                    <div class="error-message">Az adószám formátuma: 12345678-1-12</div>
                </div>
            </div>
            <div class="input-wrapper">
                <label class="input-label">Számlázási cím*</label>
                <input type="text" id="address" 
                       placeholder="1234 Város, Példa utca 123." required>
                <div class="error-message">Kérjük, adja meg a számlázási címet</div>
            </div>
            <div style="font-weight: bold;" class="section-title">Jegy adatai</div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Jegytípus*</label>
                    <select id="ticketType" required onchange="updatePrice()">
                        <option value="" disabled selected>Válasszon jegytípust</option>
                        <option value="adult-single" data-price="450">Vonaljegy - Teljes árú (450 Ft)</option>
                        <option value="adult-daily" data-price="1800">Napijegy - Teljes árú (1800 Ft)</option>
                        <option value="adult-monthly" data-price="9500">Havi bérlet - Teljes árú (9500 Ft)</option>
                        <option value="student-monthly" data-price="3450">Havi bérlet - Tanulói (3450 Ft)</option>
                        <option value="senior-monthly" data-price="3450">Havi bérlet - Nyugdíjas (3450 Ft)</option>
                    </select>
                    <div class="error-message">Kérjük, válasszon jegytípust</div>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Mennyiség*</label>
                    <input type="number" id="quantity" min="1" max="10" value="1" required
                           class="quantity-input" onchange="updatePrice()">
                    <div class="error-message">A mennyiség 1 és 10 között lehet</div>
                </div>
            </div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Érvényesség kezdete*</label>
                    <input type="date" id="validFrom" required
                           onchange="updateValidUntil()">
                    <div class="error-message">Kérjük, válasszon kezdő dátumot</div>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Érvényesség vége</label>
                    <input type="date" id="validUntil" readonly>
                </div>
            </div>
            <div style="font-weight: bold;" class="section-title">Fizetési információk</div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Fizetési mód*</label>
                    <select id="paymentMethod">
                        <option value="" disabled selected>Válasszon fizetési módot</option>
                        <option value="card">Bankkártya</option>
                        <option value="simplepay">SimplePay</option>
                        <option value="paypal">PayPal</option>
                    </select>
                    <div class="error-message">Kérjük, válasszon fizetési módot</div>
                </div>
            </div>
            <div class="input-wrapper">
                <label class="input-label">Számlaszám*</label>
                <input type="text" id="szamlaszam" placeholder="#### #### #### ####">
            </div>

            <div class="price-display">
                Végösszeg: <span id="totalPrice">0</span> Ft
            </div>
            <div class="button-group">
                <button type="button" onclick="generateInvoice()">Számla generálása</button>
            </div>
        </form>
        <div id="invoice" style="display: none;">
            <h2>Számla előnézet</h2>
            <pre id="invoiceDetails"></pre>
            <canvas id="qrcode"></canvas>
            <div class="button-group">
                <button onclick="downloadPDF()">PDF letöltése</button>
            </div>
        </div>
    </div><br>
<!-----------------------------------------Késések igazolás generálás------------------------------>
<div class="container">
        <form id="kesesigazolas">
            <h1 style="text-align:center">Késés Igazolás</h1>
            <div class="section-title">Utas adatai</div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Név*</label>
                    <input type="text" id="nev" required>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Bérletszám / Jegyszám*</label>
                    <input type="text" id="berletszam" required>
                </div>
            </div>

            <div class="section-title">Járat adatai</div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Járatszám*</label>
                    <input type="text" id="jaratszam" required>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Dátum*</label>
                    <input type="date" id="datum" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Tervezett indulás*</label>
                    <input type="time" id="tervezett_indulas" value="00:00" required>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Tényleges indulás*</label>
                    <input type="time" id="tenyleges_indulas" value="00:00" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Felszállás helye*</label>
                    <select type="text" id="felszallas" placeholder="pázmány péter utca 1" required></select>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Leszállás helye*</label>
                    <select type="text" id="leszallas" placeholder="füredi utcai csomópont" required></select>
                </div>
            </div>
                        <div style="color: red; font-weight: bold; text-align:center">Figyelem! 00:20 perc késés után állítható elő igazolás, azon intervallumon belül történő igazolást a KKzrt érvényteleníti, többszöri visszaélés esetén jogi következményekkel jár!</div>
            <div class="button-group">
                <button type="submit">Igazolás generálása</button>
            </div>
        </form>
    </div>
<!-- -----------------------------------------------------------------------------------------------------HTML - FOOTER------------------------------------------------------------------------------------------------ -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h2>Kaposvár közlekedés</h2>
                <p style="font-style: italic">Megbízható közlekedési szolgáltatások<br> az Ön kényelméért már több mint 50 éve.</p><br>
                <div class="social-links">
                    <a style="color: darkblue; padding:1px;" href="https://www.facebook.com/VOLANBUSZ/"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="max-width:10px;"><path fill="#00008b" d="M279.1 288l14.2-92.7h-88.9v-60.1c0-25.4 12.4-50.1 52.2-50.1h40.4V6.3S260.4 0 225.4 0c-73.2 0-121.1 44.4-121.1 124.7v70.6H22.9V288h81.4v224h100.2V288z"/></svg></a>
                    <a style="color: lightblue; padding:1px;"href="https://x.com/volanbusz_hu?mx=2"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="max-width:15px;"><path fill="#add8e6" d="M459.4 151.7c.3 4.5 .3 9.1 .3 13.6 0 138.7-105.6 298.6-298.6 298.6-59.5 0-114.7-17.2-161.1-47.1 8.4 1 16.6 1.3 25.3 1.3 49.1 0 94.2-16.6 130.3-44.8-46.1-1-84.8-31.2-98.1-72.8 6.5 1 13 1.6 19.8 1.6 9.4 0 18.8-1.3 27.6-3.6-48.1-9.7-84.1-52-84.1-103v-1.3c14 7.8 30.2 12.7 47.4 13.3-28.3-18.8-46.8-51-46.8-87.4 0-19.5 5.2-37.4 14.3-53 51.7 63.7 129.3 105.3 216.4 109.8-1.6-7.8-2.6-15.9-2.6-24 0-57.8 46.8-104.9 104.9-104.9 30.2 0 57.5 12.7 76.7 33.1 23.7-4.5 46.5-13.3 66.6-25.3-7.8 24.4-24.4 44.8-46.1 57.8 21.1-2.3 41.6-8.1 60.4-16.2-14.3 20.8-32.2 39.3-52.6 54.3z"/></svg></a>
                    <a style="color: red; padding:1px;"href="https://www.instagram.com/volanbusz/"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="max-width:15px;"><path fill="#ff0000" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg></a>
                </div>
            </div>
            <div  class="footer-section">
                <h3>Elérhetőség</h3>
                <ul class="footer-links">
                    <li><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="max-width:17px;"><path fill="#ffffff" d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg> +36-82/411-850</li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="max-width:17px;"><path fill="#ffffff" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg> titkarsag@kkzrt.hu</li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="max-width:16px;"><path fill="#ffffff" d="M172.3 501.7C27 291 0 269.4 0 192 0 86 86 0 192 0s192 86 192 192c0 77.4-27 99-172.3 309.7-9.5 13.8-29.9 13.8-39.5 0zM192 272c44.2 0 80-35.8 80-80s-35.8-80-80-80-80 35.8-80 80 35.8 80 80 80z"/></svg> 7400 Kaposvár, Cseri út 16.</li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="max-width:16px;"><path fill="#ffffff" d="M172.3 501.7C27 291 0 269.4 0 192 0 86 86 0 192 0s192 86 192 192c0 77.4-27 99-172.3 309.7-9.5 13.8-29.9 13.8-39.5 0zM192 272c44.2 0 80-35.8 80-80s-35.8-80-80-80-80 35.8-80 80 35.8 80 80 80z"/></svg> Áchim András utca 1.</li>
                </ul>
            </div>
        </div>
        <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <p>© 2024 Kaposvár közlekedési Zrt. Minden jog fenntartva.</p>
        </div>
    </footer>
<!-- -----------------------------------------------------------------------------------------------------FOOTER END--------------------------------------------------------------------------------------------------- -->
    
 <script src="buy.js"></script>
</body>
</html>
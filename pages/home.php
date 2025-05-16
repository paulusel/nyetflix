<?php

require_once __DIR__ . '/../backend/helpers.php';

try {
    $user = idetifyUser(false);
    if(!isset($user['profile'])) {
        header("Location: profile.php");
    }
}
catch(Exception $e) {
    header("Location: signin.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home - Netflix</title>
    <link href="./assets/images/Icon/Icon.ico" rel="icon" type="image/x-icon" />
    <link href="./styles/index.css" rel="stylesheet" type="text/css" />
    <link href="./styles/main.css" rel="stylesheet" type="text/css" />
    <link href="./styles/nav.css" rel="stylesheet" type="text/css" />
    <link href="./styles/content-row.css" rel="stylesheet" type="text/css" />
    <link href="./styles/top.css" rel="stylesheet" type="text/css" />
    <link href="./styles/footer.css" rel="stylesheet" type="text/css" />

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <style>
        .loading {
            opacity: 0.5;
            pointer-events: none;
        }
        .error-message {
            background-color: #e50914;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
    <body>
        <header>
            <nav>
                <div class="left">
                    <img src="./assets/images/Icon/logo.png" class="brand" />
                    <div class="nav-item small">Browse</div>
                    <ul class="nav-items">
                        <a href="home.php" class = "nav-items"><li class="nav-item">Home</li></a>
                        <a href="TvSeries.php" class = "nav-items"><li class="nav-item">TV Shows</li></a>
                        <a href="#" class = "nav-items"><li class="nav-item">Movies</li></a>
                        <a href="#" class = "nav-items"><li class="nav-item">Latest</li></a>
                        <a href="#" class = "nav-items"><li class="nav-item">My List</li></a>
                        <a href="#" class = "nav-items"><li class="nav-item">Browse by Languages</li></a>
                    </ul>
                </div>
                <div class="right">
                    <ul class="nav-icons">
                        <div class="nav-item icon">
                            <i data-feather="search"></i>
                        </div>
                        <div class="nav-item icon">
                            <i data-feather="bell"></i>
                        </div>
                        <div class="nav-item icon">
                            <!-- src is handled by javascript -->
                            <img
                            alt="profile icon"
                            />
                            <i class="hide" data-feather="chevron-down"></i>
                        </div>
                    </ul>
                </div>
            </nav>
        </header>

        <main>
          <div class="top">
            <img
              src="./assets/images/Main/Top/The Grey Man.jpg"
              alt="background-image"
              class="bg-image"
          />
          <div class="dark-left"></div>
          <div class="dark-bottom"></div>

          <div class="billboard">
            <img
              src="./assets/images/Main/Top/billboard.jpg"
              alt="bg-img"
            />
            <div class="title">Watch Now</div>
            <div class="description">
              A CIA agent with an matched skills. A rogue enemy with a conscience. From Bangkok to Berlin, Vienna to Prague, it's a deadly game of cat and mouse.
            </div>
            <div class="buttons">
              <button class="play-btn">
                <i class="fa fa-play"></i>
                <div>Play</div>
              </button>

              <button class="info-btn">
                <i data-feather="info"></i>
                <div>More Info</div>
              </button>
            </div>
          </div>
          <div class="content-row column">
            <div class="title">Get In on the Action</div>
            <div class="slider">
              <div class="content">
                <div class="wrapper__front">
                  <img src="./assets/images/Main/Body/Row1/Prison-Break.jpg" alt="Prison Break" />
                </div>
                <div class="wrapper__back">
                  <div class="card__header">
                    <img src="./assets/images/Main/Body/Row1/Prison-Break.jpg" alt="Prison Break" />
                  </div>
                  <div class="card__body">
                    <div class="flex justify-between items-center">
                      <div>
                        <button class="btn btn--transparent btn--circle">
                          <svg class="card__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path d="M21.44 10.72L5.96 2.98A1.38 1.38 0 004 4.213v15.474a1.373 1.373 0 002 1.233l15.44-7.74a1.38 1.38 0 000-2.467v.007z" />
                          </svg>
                        </button>
                        <button class="btn btn--transparent btn--circle">
                          <svg class="card__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path d="M12 0a1.5 1.5 0 011.5 1.5v9h9a1.5 1.5 0 110 3h-9v9a1.5 1.5 0 11-3 0v-9h-9a1.5 1.5 0 110-3h9v-9A1.5 1.5 0 0112 0z" />
                          </svg>
                        </button>
                      </div>
                      <button class="btn btn--transparent btn--circle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="card__icon" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                          <path fill-rule="evenodd" d="M2.469 6.969a.75.75 0 011.062 0L12 15.439l8.469-8.47a.75.75 0 111.062 1.062l-9 9a.75.75 0 01-1.062 0l-9-9a.75.75 0 010-1.062z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                    <p class="card__title text">
                      <span class="text--bold">S1:E1</span>
                      Prison Break
                    </p>
                    <div class="card__progress flex justify-between items-center">
                      <div class="progressbar">
                        <div class="progressbar__status"></div>
                      </div>
                      <span class="text text--bold text--muted">51 of 52m</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="content">
                <img class = "item"
                  src="./assets/images/Main/Body/Row1/TOP-BOY.jpg"
                />
              </div>
              <div class="content">
                <img class = "item"
                  src="./assets/images/Main/Body/Row1/Money-Heist.jpg"
                />
              </div>
              <div class="content">
                <img class = "item"
                src="./assets/images/Main/Body/Row1/Night-Agent.jpg"
                />
              </div>
              <div class="content">
                <img class = "item"
                src="./assets/images/Main/Body/Row1/The Rookie.jpg"
                />
              </div>
              <div class="content">
                <img class = "item"
                src="./assets/images/Main/Body/Row1/Vikings.jpg"
                />
              </div>
              <div class="content">
                <img class = "item"
                src="./assets/images/Main/Body/Row1/SUPACELL.jpg"
                />
              </div>
              <div class="content">
                <img class = "item"
                src="./assets/images/Main/Body/Row1/The-Union.jpg"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="content-row column">
          <div class="title">New on Netflix</div>
          <div class="slider">
            <div class="content">
              <img class = "item"
                src="./assets/images/Main/Body/Row2/Squid-Game.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row2/Sakamoto-Days.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row2/Outer Banks.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row2/Bad-Guys.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row2/Carry On.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row2/Ticket to Paradise.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row2/Ad Vitam.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row2/Back in Action.jpg"
              />
            </div>
          </div>
        </div>
        <div class="content-row column">
          <div class="title">Golden Globe Award-winning TV Comedies</div>
          <div class="slider">
            <div class="content">
              <img class = "item"
                src="./assets/images/Main/Body/Row5/Baby Reindeer.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row5/Beef.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row5/The Kominsky Method.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row5/Seinfield.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row5/Friends.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row5/Monk.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row5/Mastor of None.jpg"
              />
            </div>
          </div>
        </div>
        <div class="content-row column">
          <div class="title">Epic Worlds</div>
          <div class="slider">
            <div class="content">
              <img class = "item"
                src="./assets/images/Main/Body/Row6/Love and Monsters.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row6/MIB international.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row6/One Piece.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row6/Dungeons and Dragons.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row6/The last Air-Bender.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row6/Divergent.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row6/The Witcher.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row6/3 Body Problems.jpg"
              />
            </div>
          </div>
        </div>
        <div class="content-row column">
          <div class="title">Top Searches</div>
          <div class="slider">
            <div class="content">
              <img class = "item"
                src="./assets/images/Main/Body/Row7/Anyone but you.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row7/White Chicks.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row7/Hotel transylvania.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row7/The six triple Eight.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row7/Knight and Day.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row7/Little.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row7/LiarLiar.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row7/Lift.jpg"
              />
            </div>
          </div>
        </div>

        <div class="content-row column">
          <div class="title">Your Next Watch</div>
          <div class="slider">
            <div class="content">
              <img class = "item"
                src="./assets/images/Main/Body/Row3/Gifted.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row3/Narcos.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row3/Bodjack Horseman.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row3/Instant Family.webp"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row3/Spiderman.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row3/Stranger-Things.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row3/The Social Network.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row3/Space Force.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row3/Sherlock.jpg"
              />
            </div>
          </div>
        </div>
        <div class="content-row column">
          <div class="title">Sci-Fi & Fantasy Movies</div>
          <div class="slider">
            <div class="content">
              <img class = "item"
                src="./assets/images/Main/Body/Row4/Bright.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row4/The School for Good and Evil.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row4/Jurassic-World.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row4/BumbleBee.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row4/The day after Tomorrow.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row4/Predator.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row4/Transformers.jpg"
              />
            </div>
            <div class="content">
              <img class = "item"
              src="./assets/images/Main/Body/Row4/Don't Look UP.jpg"
              />
            </div>

          </div>
        </div>
      </main>

      <footer class="column">
        <div class="social-icons row">
          <i class="fa fa-facebook social-icon"></i>
          <i class="fa fa-instagram social-icon"></i>
          <i class="fa fa-youtube social-icon"></i>
        </div>

        <div class="footer-links">
            <a href="#" class="footer-link">Audio Description</a>
            <a href="#" class="footer-link">Help Center</a>
            <a href="#" class="footer-link">Gift Cards</a>
            <a href="#" class="footer-link">Media Center</a>
            <a href="#" class="footer-link">Investor Relations</a>
            <a href="#" class="footer-link">Jobs</a>
            <a href="#" class="footer-link">Terms of use</a>
            <a href="#" class="footer-link">Privacy</a>
            <a href="#" class="footer-link">Legal Notices</a>
            <a href="#" class="footer-link">Cookie Preferences</a>
            <a href="#" class="footer-link">Coorperate Information</a>
            <a href="#" class="footer-link">Contact us</a>
        </div>

        <div class="footer-service">
            <button class="service-code">Service Code</button>
        </div>

            <div class="footer-copyright">
            &copy; 2025 Netflix, Inc.
            </div>
      </footer>

      <script type="module" src="./scripts/script.js"></script>
      <script src="https://unpkg.com/feather-icons"></script>
      <script type="module">
        import ui from './scripts/ui.js';
        
        // Initialize Feather icons
        feather.replace();
        
        // Initialize UI
        document.addEventListener('DOMContentLoaded', () => {
            ui.init();
        });
      </script>
    </body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claggy Power</title>
    <style>
        body {
            background-color: black;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            color: white;
            margin-bottom: 20px;
        }
        label, p {
            color: white;
        }
        input[type="text"], input[type="email"] {
            background-color: #333;
            color: white;
            border: 1px solid #555;
        }
        .styled-button , .odds-button {
            background-color: #5a259d;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 25px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .styled-button:hover, .odds-button:hover  {
            background-color: #5b6eae;
            transform: translateY(-3px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.3);
        }
        img {
            width: 100%;
            height: 50%;
            object-fit: cover;
        }
        #matches {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
            width: 80%;
        }

        .match-card {
            background-color: #333;
            color: white;
            border: 1px solid #555;
            border-radius: 15px;
            padding: 20px;
            margin: 10px;
            width: 30%;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s ease;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <img src="banner.png" alt="Banner Image" style="width: 100%; height: 50%;">

    <form id="dataForm">
        <button class="styled-button" onclick="submitData()">LINK TO DISCORD</button>
    </form>
    <div id="result"></div>

    <h2>UPCOMING FIXTURES</h2>
    <div id="matches"></div>

    <script>
        function loadMatches() {
            fetch('http://127.0.0.1:5000/premiership_matches')
            .then(response => response.json())
            .then(matches => {
                console.log(matches);
                
                const matchesDiv = document.getElementById('matches');
                matchesDiv.innerHTML = '';
                
                matches.forEach(match => {
                    const matchElement = document.createElement('div');
                    matchElement.classList.add('match-card');
                    matchElement.innerHTML = `
                        <h3>${match.home_team} vs ${match.away_team}</h3>
                        <p>Date: ${match.date}</p>
                        <div class="odds-buttons">
                            <button class="odds-button">Win: ${match.odds_win}</button>
                            <button class="odds-button">Draw: ${match.odds_draw}</button>
                            <button class="odds-button">Lose: ${match.odds_lose}</button>
                        </div>
                    `;
                    matchesDiv.appendChild(matchElement);
                });
            })
            .catch(error => {
                console.error('Error fetching matches:', error);
            });
        }

        window.onload = loadMatches;
    </script>
</body>
</html>
from flask import Flask, jsonify
from flask_cors import CORS
import requests
import datetime
import json
import os

app = Flask(__name__)
CORS(app)

JSON_FILE = 'current_fixtures.json'
MAX_WEEKS_TO_CHECK = 4

def fetch_and_save_fixtures():
    odds_api = "b56111f12ec05c982025d4680f42a849"
    url = "https://api.the-odds-api.com/v4/sports/soccer_epl/odds"
    
    params = {
        'apiKey': odds_api,
        'regions': 'uk',
        'markets': 'h2h',
        'oddsFormat': 'decimal',
        'dateFormat': 'unix',
    }

    fixtures_found = False
    for week in range(MAX_WEEKS_TO_CHECK):
        start_date = datetime.date.today() + datetime.timedelta(weeks=week)
        end_date = start_date + datetime.timedelta(days=7)
        params['dateFrom'] = start_date.isoformat()
        params['dateTo'] = end_date.isoformat()

        response = requests.get(url, params=params)
        
        if response.status_code != 200:
            print(f"Error fetching data: {response.status_code}")
            return

        data = response.json()
        matches = []

        for event in data:
            match_date = datetime.datetime.fromtimestamp(event['commence_time'])
            
            if start_date <= match_date.date() <= end_date:
                home_team = event['home_team']
                away_team = event['away_team']
                
                if not event['bookmakers']:
                    continue

                odds = event['bookmakers'][0]['markets'][0]['outcomes']
                
                odds_win = next((o['price'] for o in odds if o['name'] == home_team), None)
                odds_draw = next((o['price'] for o in odds if o['name'].lower() == 'draw'), None)
                odds_lose = next((o['price'] for o in odds if o['name'] == away_team), None)

                match_info = {
                    'home_team': home_team,
                    'away_team': away_team,
                    'date': match_date.strftime('%Y-%m-%d %H:%M'),
                    'odds_win': odds_win,
                    'odds_draw': odds_draw,
                    'odds_lose': odds_lose
                }
                matches.append(match_info)

        if matches:
            fixtures_found = True
            break

    if fixtures_found:
        with open(JSON_FILE, 'w') as f:
            json.dump(matches, f)
        print("Fixtures saved to JSON file.")
    else:
        print("No fixtures found for the upcoming weeks.")

def is_json_file_empty(file_path):
    if not os.path.exists(file_path):
        return True
    
    with open(file_path, 'r') as f:
        try:
            data = json.load(f)
            return not bool(data)
        except ValueError:
            return True

@app.route('/premiership_matches', methods=['GET'])
def get_premiership_matches():
    if is_json_file_empty(JSON_FILE):
        fetch_and_save_fixtures()
    
    with open(JSON_FILE, 'r') as f:
        matches = json.load(f)
    
    return jsonify(matches)

if __name__ == '__main__':
    if datetime.date.today().weekday() == 0 or is_json_file_empty(JSON_FILE):
        fetch_and_save_fixtures()
    app.run(debug=True)
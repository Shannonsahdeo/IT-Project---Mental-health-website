from flask import Flask, render_template, request
import spotipy
from spotipy.oauth2 import SpotifyOAuth

app = Flask(__name__)

# Set your Spotify API credentials
SPOTIPY_CLIENT_ID = "59d7e739d4cc410f9251a582ec395c3b"
SPOTIPY_CLIENT_SECRET = "bcbcf7755f19417fa03b9a130e7f8d02"
SPOTIPY_REDIRECT_URI = "http://localhost:8888/callback"

# Set up Spotify API client
sp = spotipy.Spotify(auth_manager=SpotifyOAuth(
    client_id=SPOTIPY_CLIENT_ID,
    client_secret=SPOTIPY_CLIENT_SECRET,
    redirect_uri=SPOTIPY_REDIRECT_URI,
    scope=["user-library-read", "playlist-modify-public", "playlist-modify-private"]
))

@app.route("/", methods=["GET"])
def home():
    return render_template("index.html")

@app.route("/music-recommendation", methods=["GET"])
def music_recommendation():
    return render_template("index.html")

@app.route("/recommend", methods=["POST"])
def recommend():
    emotion = request.form.get("emotion")
    
    # Fetch 10 playlists based on emotion
    playlists = fetch_spotify_playlists(emotion, limit=10)

    # If no playlists found, display a message
    if not playlists:
        message = "No playlists found for this mood."
        return render_template("recommendations.html", playlists=[], message=message)

    return render_template("recommendations.html", playlists=playlists)

def fetch_spotify_playlists(emotion, limit=10):
    """Fetch playlists from Spotify based on the user's selected emotion."""
    try:
        query = emotion
        results = []
        offset = 0

        while len(results) < limit:
            result = sp.search(q=query, type="playlist", limit=min(limit - len(results), 50), offset=offset)
            playlists = result.get("playlists", {}).get("items", [])

            # Append playlists to results and increment offset
            results.extend(playlists)
            offset += len(playlists)

            # If no more results, break the loop
            if len(playlists) == 0:
                break

        return results

    except Exception as e:
        print(f"Error fetching playlists: {e}")
        return []

if __name__ == "__main__":
    app.run(debug=True)

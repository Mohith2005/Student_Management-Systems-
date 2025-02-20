from flask import Flask, render_template, request, redirect, url_for
import pandas as pd

app = Flask(__name__)

@app.route('/')
def home():
    return render_template('login.html')  # Changed from index.html to login.html

@app.route('/login', methods=['POST'])
def handle_login():
    username = request.form['username']
    password = request.form['password']
    user_type = request.form['user_type']

    if user_type == 'student':
        df = pd.read_csv('student.csv')
    elif user_type == 'faculty':
        df = pd.read_csv('faculty.csv')
    else:
        return "Invalid user type", 400

    user = df[(df['Username'] == username) & (df['Password'] == password)]

    if not user.empty:
        return f"Welcome {username}"
    else:
        return "Invalid credentials", 401

if __name__ == '__main__':
    app.run(debug=True)

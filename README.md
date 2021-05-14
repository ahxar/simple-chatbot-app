# Simple ChatBot App, Larasocket + Wit.ai

## Installation

```
git clone https://github.com/faropedia/simple-chatbot-app
cd simple-chatbot-app
cp .env.example .env

php artisan key:generate

# create database named 'simple_chat_app'
php artisan migrate --seed

npm install
npm run dev

php artisan serve
```

## Usage

1. Register first (http://localhost:8000/register)
2. After login, go to http://localhost:8000/messages
3. Send greeting text (required your name): "Hello, my name is `{your name}`, thank you"
4. Bot will reply with "Hello `{your name}`, nice to meet you!"

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

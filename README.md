# Thunderbite Backend Test

This test evaluates your Laravel development skills with a focus on problem-solving,
code quality, and attention to detail.

## Installation

1. Clone this repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan key:generate`
5. Run `php artisan migrate`
6. Run `php artisan db:seed`
7. Run `npm install && npm run build`
8. Visit `/backstage` in your browser

Login credentials:
- Email: `test@thunderbite.com`
- Password: `test123`

---

## The Task: Scratch Card Game

The database seeder creates two test campaigns, 18 prize records, and 10,000 game records.

Access the first test campaign at:

```
{your-app-url}/test-campaign-1?a=account&segment=low
```

You will see a board of 25 squares (5x5 grid). The game works as follows:

- A player clicks tiles on the board to reveal prizes
- Each revealed tile corresponds to a prize from the back office
- When a player collects **three matching tiles**, the game ends and they win that prize
- The game should be properly connected to the database; the provided API endpoint is a simplified example using cache

### Requirements

1. **Game Creation**: When a player accesses the campaign link with `?a=account`,
   create a new game or retrieve their unfinished game.

2. **Tile Selection**: Prizes are selected based on their configured weight. Use:
   ```
   ->orderByRaw('-LOG(RAND()) / weight')
   ```
   Consider how to create the illusion of equal chance for prizes until the final match.

3. **Prize Daily Limits**: Enforce daily volume limits for prizes.
   If a prize has reached its daily cap, it cannot be won again that day.

4. **Campaign Validity**: Display a message if the campaign has not started or has already ended.

5. **Segment Filtering**: Players can only draw prizes matching their `segment`
   query parameter (`low`, `med`, `high`).

6. **Game Persistence**: The game state must survive page refreshes.
   When a player returns, they should see their previously revealed tiles.

7. **Prize Images**: Each prize has an associated tile image stored in the database.
   Use these images when revealing tiles.

### API Contract

The frontend sends POST requests to the API path with:
```json
{ "gameId": 0, "tileIndex": 0 }
```

Expected response:
```json
{ "tileImage": "/assets/tile.jpg" }
```

When the game ends (3rd match), include a message:
```json
{ "tileImage": "/assets/tile.jpg", "message": "You won a prize!" }
```

The frontend configuration expects a JSON string:
```json
{
    "apiPath": "/api/flip",
    "gameId": "gameID",
    "revealedTiles": [{ "index": 0, "image": "/assets/tile.jpg" }],
    "message": "Campaign has ended"
}
```

---

## Guidelines

- Use new migrations for any database changes you need to make
- You are encouraged to optimize database queries and structure
- Focus on code clarity, security, and maintainability
- Look for and fix any issues you find in the existing codebase
- If you have questions, contact us via email

## Evaluation

We evaluate based on:
1. Code clarity and maintainability
2. Adherence to Laravel best practices
3. Problem-solving and attention to detail
4. Correct and complete feature implementation

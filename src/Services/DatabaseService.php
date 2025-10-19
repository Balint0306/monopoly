<?php

namespace App\Services;

use PDO;
use PDOException;

class DatabaseService
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            // Get the database URL from Render's environment variables
            $databaseUrl = getenv('DATABASE_URL');
            if ($databaseUrl === false) {
                // Fallback for local development if you create a .env file
                // In a real app, use a library like vlucas/phpdotenv
                $databaseUrl = getenv('DB_CONNECTION_STRING');
                 if ($databaseUrl === false) {
                    die("DATABASE_URL environment variable is not set.");
                }
            }

            $dbopts = parse_url($databaseUrl);

            $host = $dbopts["host"];
            $port = $dbopts["port"];
            $dbname = ltrim($dbopts["path"], '/');
            $user = $dbopts["user"];
            $pass = $dbopts["pass"];

            try {
                self::$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // In a real app, log this error instead of dying
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    /**
     * Creates the 'games' table if it doesn't exist.
     */
    public static function initDatabase(): void
    {
        $pdo = self::getConnection();
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS games (
                id SERIAL PRIMARY KEY,
                game_state JSON NOT NULL,
                created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Retrieves a game's state from the database.
     */
    public static function getGame(int $id): ?string
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT game_state FROM games WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['game_state'] : null;
    }

    /**
     * Saves a game's state to the database.
     */
    public static function saveGame(int $id, string $gameState): void
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("
            UPDATE games 
            SET game_state = :game_state, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id, ':game_state' => $gameState]);
    }

    /**
     * Creates a new game in the database and returns its ID.
     */
    public static function createGame(string $gameState): int
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("INSERT INTO games (game_state) VALUES (:game_state) RETURNING id");
        $stmt->execute([':game_state' => $gameState]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }
}

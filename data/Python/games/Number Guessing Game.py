"""
Number Guessing Game
A fun number guessing game with hints and scoring.
"""

import random
import time
from typing import Dict, List

class NumberGuessingGame:
    """Number guessing game with multiple difficulty levels."""
    
    def __init__(self):
        """Initialize the game."""
        self.score = 0
        self.games_played = 0
        self.history: List[Dict] = []
        self.current_number = None
        self.max_attempts = None
        self.min_range = None
        self.max_range = None
        self.attempts_used = 0
        self.start_time = None
    
    def start_game(self):
        """Start the game menu."""
        print("🎮 Number Guessing Game")
        print("=" * 40)
        print("Try to guess the secret number!")
        print()
        
        while True:
            print("\nMain Menu:")
            print("1. Easy (1-50, 15 attempts)")
            print("2. Medium (1-100, 10 attempts)")
            print("3. Hard (1-200, 8 attempts)")
            print("4. Custom Range")
            print("5. View Statistics")
            print("6. View History")
            print("7. Instructions")
            print("8. Exit")
            
            choice = input("\nSelect option (1-8): ").strip()
            
            if choice == "1":
                self.play_game(1, 50, 15, "Easy")
            elif choice == "2":
                self.play_game(1, 100, 10, "Medium")
            elif choice == "3":
                self.play_game(1, 200, 8, "Hard")
            elif choice == "4":
                self.custom_game()
            elif choice == "5":
                self.show_statistics()
            elif choice == "6":
                self.show_history()
            elif choice == "7":
                self.show_instructions()
            elif choice == "8":
                print("Thanks for playing! 👋")
                break
            else:
                print("Invalid option. Please try again.")
    
    def play_game(self, min_range: int, max_range: int, max_attempts: int, difficulty: str):
        """Play a single game round."""
        self.current_number = random.randint(min_range, max_range)
        self.max_attempts = max_attempts
        self.min_range = min_range
        self.max_range = max_range
        self.attempts_used = 0
        self.start_time = time.time()
        
        print(f"\n🎯 {difficulty} Mode")
        print(f"I'm thinking of a number between {min_range} and {max_range}")
        print(f"You have {max_attempts} attempts to guess it!")
        print()
        
        while self.attempts_used < self.max_attempts:
            remaining_attempts = self.max_attempts - self.attempts_used
            print(f"Attempts remaining: {remaining_attempts}")
            
            try:
                guess = int(input("Enter your guess: "))
                
                if not (min_range <= guess <= max_range):
                    print(f"Please enter a number between {min_range} and {max_range}")
                    continue
                
                self.attempts_used += 1
                
                if guess == self.current_number:
                    self._win_game()
                    break
                elif guess < self.current_number:
                    print("📈 Too low! Try a higher number.")
                else:
                    print("📉 Too high! Try a lower number.")
                
                # Give hints after certain attempts
                if self.attempts_used >= 3:
                    self._give_hint()
                
                print()
            
            except ValueError:
                print("Please enter a valid number!")
        
        if self.attempts_used >= self.max_attempts and guess != self.current_number:
            self._lose_game()
    
    def custom_game(self):
        """Set up a custom game."""
        print("\nCustom Game Setup")
        print("-" * 20)
        
        try:
            min_range = int(input("Enter minimum number: "))
            max_range = int(input("Enter maximum number: "))
            
            if min_range >= max_range:
                print("Maximum must be greater than minimum!")
                return
            
            max_attempts = int(input("Enter maximum attempts: "))
            
            if max_attempts < 1:
                print("At least 1 attempt required!")
                return
            
            self.play_game(min_range, max_range, max_attempts, "Custom")
        
        except ValueError:
            print("Please enter valid numbers!")
    
    def _give_hint(self):
        """Give contextual hints based on remaining attempts."""
        difference = abs(self.current_number - (self.min_range + self.max_range) // 2)
        
        if self.attempts_used >= self.max_attempts - 2:
            # Last attempts - give more specific hints
            if self.current_number % 2 == 0:
                print("💡 Hint: The number is EVEN")
            else:
                print("💡 Hint: The number is ODD")
        
        if self.attempts_used >= self.max_attempts // 2:
            # Halfway through - give range hints
            quarter = (self.max_range - self.min_range) // 4
            mid_point = (self.min_range + self.max_range) // 2
            
            if self.current_number < mid_point - quarter:
                print("💡 Hint: The number is in the LOWER quarter")
            elif self.current_number < mid_point:
                print("💡 Hint: The number is in the LOWER half")
            elif self.current_number < mid_point + quarter:
                print("💡 Hint: The number is in the UPPER half")
            else:
                print("💡 Hint: The number is in the UPPER quarter")
    
    def _win_game(self):
        """Handle winning the game."""
        end_time = time.time()
        time_taken = int(end_time - self.start_time)
        
        # Calculate score
        base_score = 100
        attempts_penalty = (self.attempts_used - 1) * 5
        time_penalty = min(time_taken // 10, 20)  # 1 point per 10 seconds, max 20 points
        difficulty_bonus = (self.max_range - self.min_range) // 10
        
        game_score = max(10, base_score - attempts_penalty - time_penalty + difficulty_bonus)
        self.score += game_score
        
        print("\n🎉 CONGRATULATIONS! 🎉")
        print(f"You guessed the number {self.current_number} correctly!")
        print(f"Attempts used: {self.attempts_used}")
        print(f"Time taken: {time_taken} seconds")
        print(f"Score earned: {game_score}")
        print(f"Total score: {self.score}")
        
        # Save to history
        self._save_game_result(True, time_taken)
    
    def _lose_game(self):
        """Handle losing the game."""
        print("\n😔 GAME OVER 😔")
        print(f"The number was {self.current_number}")
        print("Better luck next time!")
        
        # Save to history
        self._save_game_result(False, 0)
    
    def _save_game_result(self, won: bool, time_taken: int):
        """Save game result to history."""
        self.games_played += 1
        
        result = {
            'game_number': self.games_played,
            'difficulty': f"{self.min_range}-{self.max_range}",
            'won': won,
            'attempts_used': self.attempts_used,
            'max_attempts': self.max_attempts,
            'time_taken': time_taken,
            'secret_number': self.current_number if not won else None
        }
        
        self.history.append(result)
    
    def show_statistics(self):
        """Display game statistics."""
        if self.games_played == 0:
            print("\nNo games played yet!")
            return
        
        wins = sum(1 for game in self.history if game['won'])
        win_rate = (wins / self.games_played) * 100
        avg_attempts = sum(game['attempts_used'] for game in self.history) / self.games_played
        total_time = sum(game['time_taken'] for game in self.history)
        
        print("\n📊 Game Statistics")
        print("=" * 30)
        print(f"Games Played: {self.games_played}")
        print(f"Games Won: {wins}")
        print(f"Games Lost: {self.games_played - wins}")
        print(f"Win Rate: {win_rate:.1f}%")
        print(f"Total Score: {self.score}")
        print(f"Average Attempts: {avg_attempts:.1f}")
        print(f"Total Time: {total_time} seconds")
        
        if self.history:
            best_game = max(self.history, key=lambda x: x['attempts_used'] if x['won'] else float('inf'))
            worst_game = min(self.history, key=lambda x: x['attempts_used'] if x['won'] else float('inf'))
            
            print(f"\nBest Performance: {best_game['attempts_used']} attempts")
            print(f"Worst Performance: {worst_game['attempts_used']} attempts")
    
    def show_history(self):
        """Display game history."""
        if not self.history:
            print("\nNo game history yet!")
            return
        
        print("\n📜 Game History")
        print("=" * 80)
        print(f"{'Game':<5} {'Difficulty':<12} {'Result':<8} {'Attempts':<10} {'Time':<8} {'Secret':<8}")
        print("-" * 80)
        
        for game in self.history[-10:]:  # Show last 10 games
            result = "Won" if game['won'] else "Lost"
            secret = str(game['secret_number']) if game['secret_number'] else "---"
            
            print(f"{game['game_number']:<5} {game['difficulty']:<12} {result:<8} "
                  f"{game['attempts_used']}/{game['max_attempts']:<10} {game['time_taken']:<8}s {secret:<8}")
        
        if len(self.history) > 10:
            print(f"\n... and {len(self.history) - 10} more games")
    
    def show_instructions(self):
        """Display game instructions."""
        print("\n📖 How to Play")
        print("=" * 30)
        print("1. The game will think of a random number in a specified range")
        print("2. You need to guess the number within the given attempts")
        print("3. After each guess, you'll be told if your guess is too high or too low")
        print("4. Hints will be provided as you use more attempts")
        print("5. Your score is based on:")
        print("   - Fewer attempts = Higher score")
        print("   - Less time = Higher score")
        print("   - Higher difficulty = Higher score")
        print("\n💡 Tips:")
        print("- Use binary search: Guess the middle number first")
        print("- Pay attention to the hints")
        print("- Remember the range of possible numbers")
        print("- Practice improves your performance!")

class MultiPlayerGame:
    """Multiplayer version of the number guessing game."""
    
    def __init__(self):
        """Initialize multiplayer game."""
        self.players = []
        self.current_player_index = 0
        self.secret_number = None
        self.round_number = 1
    
    def add_player(self, name: str):
        """Add a player to the game."""
        self.players.append({
            'name': name,
            'score': 0,
            'wins': 0,
            'attempts': []
        })
    
    def play_multiplayer(self):
        """Play multiplayer game."""
        print("\n👥 Multiplayer Mode")
        print("=" * 30)
        
        # Get players
        num_players = int(input("Number of players (2-4): "))
        num_players = max(2, min(4, num_players))
        
        for i in range(num_players):
            name = input(f"Enter name for Player {i+1}: ")
            self.add_player(name)
        
        # Game settings
        min_range = int(input("Minimum number: "))
        max_range = int(input("Maximum number: "))
        max_attempts = int(input("Maximum attempts per player: "))
        
        # Choose who sets the number
        print(f"\n{self.players[0]['name']} will set the secret number first")
        self.secret_number = int(input(f"{self.players[0]['name']}, enter the secret number: "))
        
        # Clear screen (simulate)
        print("\n" * 50)
        
        # Game loop
        for round_num in range(1, max_attempts * num_players + 1):
            current_player = self.players[self.current_player_index]
            
            print(f"\nRound {round_num} - {current_player['name']}'s turn")
            print(f"Range: {min_range} to {max_range}")
            
            try:
                guess = int(input("Enter your guess: "))
                current_player['attempts'].append(guess)
                
                if guess == self.secret_number:
                    print(f"🎉 {current_player['name']} guessed it!")
                    current_player['score'] += 100 - len(current_player['attempts'])
                    current_player['wins'] += 1
                    self._show_multiplayer_scores()
                    break
                elif guess < self.secret_number:
                    print("📈 Too low!")
                else:
                    print("📉 Too high!")
            
            except ValueError:
                print("Please enter a valid number!")
                continue
            
            # Next player
            self.current_player_index = (self.current_player_index + 1) % len(self.players)
        
        else:
            print(f"\nNo one guessed the number! It was {self.secret_number}")
            self._show_multiplayer_scores()
    
    def _show_multiplayer_scores(self):
        """Display multiplayer scores."""
        print("\n🏆 Final Scores")
        print("=" * 30)
        
        sorted_players = sorted(self.players, key=lambda x: x['score'], reverse=True)
        
        for i, player in enumerate(sorted_players, 1):
            print(f"{i}. {player['name']}: {player['score']} points ({player['wins']} wins)")

def main():
    """Main entry point."""
    print("🎮 Number Guessing Game Collection")
    print("=" * 40)
    
    while True:
        print("\nGame Selection:")
        print("1. Single Player")
        print("2. Multiplayer")
        print("3. Exit")
        
        choice = input("Select option (1-3): ").strip()
        
        if choice == "1":
            game = NumberGuessingGame()
            game.start_game()
        elif choice == "2":
            game = MultiPlayerGame()
            game.play_multiplayer()
        elif choice == "3":
            print("Thanks for playing! 👋")
            break
        else:
            print("Invalid option. Please try again.")

if __name__ == "__main__":
    main()

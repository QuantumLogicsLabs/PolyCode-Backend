"""
Tic-Tac-Toe Game
Classic two-player Tic-Tac-Toe game with AI opponent.
"""

import random
from typing import List, Tuple, Optional

class TicTacToe:
    """Tic-Tac-Toe game implementation."""
    
    def __init__(self):
        """Initialize the game."""
        self.board = [[' ' for _ in range(3)] for _ in range(3)]
        self.current_player = 'X'
        self.game_over = False
        self.winner = None
        self.moves_count = 0
    
    def display_board(self):
        """Display the game board."""
        print("\n")
        print(f" {self.board[0][0]} | {self.board[0][1]} | {self.board[0][2]} ")
        print("---+---+---")
        print(f" {self.board[1][0]} | {self.board[1][1]} | {self.board[1][2]} ")
        print("---+---+---")
        print(f" {self.board[2][0]} | {self.board[2][1]} | {self.board[2][2]} ")
        print("\n")
    
    def make_move(self, row: int, col: int) -> bool:
        """
        Make a move on the board.
        
        Args:
            row (int): Row index (0-2)
            col (int): Column index (0-2)
            
        Returns:
            bool: True if move was successful, False otherwise
        """
        if self.game_over:
            print("Game is already over!")
            return False
        
        if not (0 <= row < 3 and 0 <= col < 3):
            print("Invalid position! Row and column must be 0, 1, or 2.")
            return False
        
        if self.board[row][col] != ' ':
            print("Position already taken!")
            return False
        
        self.board[row][col] = self.current_player
        self.moves_count += 1
        
        # Check for winner
        if self.check_winner():
            self.game_over = True
            self.winner = self.current_player
        elif self.moves_count == 9:
            self.game_over = True
            self.winner = 'Tie'
        else:
            # Switch player
            self.current_player = 'O' if self.current_player == 'X' else 'X'
        
        return True
    
    def check_winner(self) -> bool:
        """Check if there's a winner."""
        # Check rows
        for row in self.board:
            if row[0] == row[1] == row[2] != ' ':
                return True
        
        # Check columns
        for col in range(3):
            if self.board[0][col] == self.board[1][col] == self.board[2][col] != ' ':
                return True
        
        # Check diagonals
        if self.board[0][0] == self.board[1][1] == self.board[2][2] != ' ':
            return True
        if self.board[0][2] == self.board[1][1] == self.board[2][0] != ' ':
            return True
        
        return False
    
    def get_available_moves(self) -> List[Tuple[int, int]]:
        """Get list of available moves."""
        moves = []
        for row in range(3):
            for col in range(3):
                if self.board[row][col] == ' ':
                    moves.append((row, col))
        return moves
    
    def reset_game(self):
        """Reset the game to initial state."""
        self.board = [[' ' for _ in range(3)] for _ in range(3)]
        self.current_player = 'X'
        self.game_over = False
        self.winner = None
        self.moves_count = 0

class AIPlayer:
    """AI opponent for Tic-Tac-Toe."""
    
    def __init__(self, symbol: str, difficulty: str = 'medium'):
        """
        Initialize AI player.
        
        Args:
            symbol (str): 'X' or 'O'
            difficulty (str): 'easy', 'medium', or 'hard'
        """
        self.symbol = symbol
        self.difficulty = difficulty
    
    def get_move(self, game: TicTacToe) -> Tuple[int, int]:
        """
        Get AI's next move based on difficulty.
        
        Args:
            game (TicTacToe): Current game state
            
        Returns:
            Tuple[int, int]: Row and column for the move
        """
        available_moves = game.get_available_moves()
        
        if not available_moves:
            return None
        
        if self.difficulty == 'easy':
            return self._random_move(available_moves)
        elif self.difficulty == 'medium':
            return self._medium_move(game, available_moves)
        else:  # hard
            return self._best_move(game)
    
    def _random_move(self, available_moves: List[Tuple[int, int]]) -> Tuple[int, int]:
        """Make a random move."""
        return random.choice(available_moves)
    
    def _medium_move(self, game: TicTacToe, available_moves: List[Tuple[int, int]]) -> Tuple[int, int]:
        """Medium difficulty: 70% best move, 30% random."""
        if random.random() < 0.7:
            return self._best_move(game)
        else:
            return self._random_move(available_moves)
    
    def _best_move(self, game: TicTacToe) -> Tuple[int, int]:
        """Find the best move using minimax algorithm."""
        best_score = float('-inf')
        best_move = None
        
        for move in game.get_available_moves():
            row, col = move
            game.board[row][col] = self.symbol
            
            score = self._minimax(game, 0, False)
            
            game.board[row][col] = ' '  # Undo move
            
            if score > best_score:
                best_score = score
                best_move = move
        
        return best_move
    
    def _minimax(self, game: TicTacToe, depth: int, is_maximizing: bool) -> int:
        """Minimax algorithm for finding best move."""
        # Check for terminal states
        winner = self._check_winner_temp(game)
        if winner == self.symbol:
            return 10 - depth
        elif winner == 'X' if self.symbol == 'O' else 'O':
            return depth - 10
        elif len(game.get_available_moves()) == 0:
            return 0
        
        if is_maximizing:
            max_eval = float('-inf')
            for move in game.get_available_moves():
                row, col = move
                game.board[row][col] = self.symbol
                eval = self._minimax(game, depth + 1, False)
                game.board[row][col] = ' '
                max_eval = max(max_eval, eval)
            return max_eval
        else:
            min_eval = float('inf')
            opponent = 'O' if self.symbol == 'X' else 'X'
            for move in game.get_available_moves():
                row, col = move
                game.board[row][col] = opponent
                eval = self._minimax(game, depth + 1, True)
                game.board[row][col] = ' '
                min_eval = min(min_eval, eval)
            return min_eval
    
    def _check_winner_temp(self, game: TicTacToe) -> Optional[str]:
        """Check winner without modifying game state."""
        # Check rows
        for row in game.board:
            if row[0] == row[1] == row[2] != ' ':
                return row[0]
        
        # Check columns
        for col in range(3):
            if game.board[0][col] == game.board[1][col] == game.board[2][col] != ' ':
                return game.board[0][col]
        
        # Check diagonals
        if game.board[0][0] == game.board[1][1] == game.board[2][2] != ' ':
            return game.board[0][0]
        if game.board[0][2] == game.board[1][1] == game.board[2][0] != ' ':
            return game.board[0][2]
        
        return None

class TicTacToeGame:
    """Main game controller."""
    
    def __init__(self):
        """Initialize the game."""
        self.game = TicTacToe()
        self.ai_player = None
        self.human_player = 'X'
    
    def start_game(self):
        """Start the game menu."""
        print("Welcome to Tic-Tac-Toe!")
        print("=" * 40)
        
        while True:
            print("\nMain Menu:")
            print("1. Two Players")
            print("2. Play vs AI (Easy)")
            print("3. Play vs AI (Medium)")
            print("4. Play vs AI (Hard)")
            print("5. Exit")
            
            choice = input("\nSelect option (1-5): ").strip()
            
            if choice == "1":
                self.play_two_players()
            elif choice == "2":
                self.play_vs_ai('easy')
            elif choice == "3":
                self.play_vs_ai('medium')
            elif choice == "4":
                self.play_vs_ai('hard')
            elif choice == "5":
                print("Thanks for playing!")
                break
            else:
                print("Invalid option. Please try again.")
    
    def play_two_players(self):
        """Play with two human players."""
        self.game.reset_game()
        self.ai_player = None
        
        print("\nTwo Players Mode")
        print("Player 1: X, Player 2: O")
        
        while not self.game.game_over:
            self.game.display_board()
            print(f"Player {self.game.current_player}'s turn")
            
            try:
                row = int(input("Enter row (0-2): "))
                col = int(input("Enter column (0-2): "))
                
                if self.game.make_move(row, col):
                    if self.game.game_over:
                        self._display_result()
                else:
                    print("Invalid move! Try again.")
            
            except ValueError:
                print("Please enter valid numbers (0, 1, or 2).")
    
    def play_vs_ai(self, difficulty: str):
        """Play against AI."""
        self.game.reset_game()
        self.ai_player = AIPlayer('O', difficulty)
        
        print(f"\nvs AI Mode ({difficulty.capitalize()} difficulty)")
        print("You are X, AI is O")
        
        # Randomly decide who goes first
        if random.random() < 0.5:
            human_first = True
            print("You go first!")
        else:
            human_first = False
            print("AI goes first!")
        
        while not self.game.game_over:
            self.game.display_board()
            
            if (human_first and self.game.current_player == 'X') or \
               (not human_first and self.game.current_player == 'O'):
                # Human's turn
                print("Your turn")
                
                try:
                    row = int(input("Enter row (0-2): "))
                    col = int(input("Enter column (0-2): "))
                    
                    if not self.game.make_move(row, col):
                        print("Invalid move! Try again.")
                        continue
                
                except ValueError:
                    print("Please enter valid numbers (0, 1, or 2).")
                    continue
            else:
                # AI's turn
                print("AI is thinking...")
                move = self.ai_player.get_move(self.game)
                if move:
                    row, col = move
                    self.game.make_move(row, col)
                    print(f"AI plays: ({row}, {col})")
            
            if self.game.game_over:
                self.game.display_board()
                self._display_result()
    
    def _display_result(self):
        """Display the game result."""
        print("\n" + "=" * 30)
        if self.game.winner == 'Tie':
            print("It's a TIE! 🤝")
        elif self.ai_player and self.game.winner == self.ai_player.symbol:
            print("AI WINS! 🤖")
        else:
            print(f"Player {self.game.winner} WINS! 🎉")
        print("=" * 30)

def main():
    """Main entry point."""
    game = TicTacToeGame()
    game.start_game()

if __name__ == "__main__":
    main()

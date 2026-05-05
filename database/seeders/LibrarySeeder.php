<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@library.test'],
            [
                'name' => 'Library Admin',
                'password' => 'password',
            ]
        );

        $categoryNames = [
            'Computer Science',
            'Fiction',
            'History',
            'Science',
            'Mathematics',
            'Reference',
        ];

        foreach ($categoryNames as $name) {
            Category::query()->firstOrCreate(['name' => $name]);
        }

        $books = [
            [
                'title' => 'Introduction to Algorithms',
                'author' => 'Cormen, Leiserson, Rivest, Stein',
                'description' => 'Comprehensive textbook on data structures and algorithms, widely used in university courses.',
                'category' => 'Computer Science',
                'year' => 2022,
                'keywords' => 'algorithms, data structures, complexity, textbook',
            ],
            [
                'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author' => 'Robert C. Martin',
                'description' => 'Principles and patterns for writing maintainable professional software.',
                'category' => 'Computer Science',
                'year' => 2008,
                'keywords' => 'software engineering, refactoring, best practices, agile',
            ],
            [
                'title' => 'The Midnight Library',
                'author' => 'Matt Haig',
                'description' => 'A novel about infinite possibilities between life and death in a mysterious library.',
                'category' => 'Fiction',
                'year' => 2020,
                'keywords' => 'fiction, novel, mental health, library, possibilities',
            ],
            [
                'title' => 'Sapiens: A Brief History of Humankind',
                'author' => 'Yuval Noah Harari',
                'description' => 'Explores how Homo sapiens came to dominate Earth through culture and cooperation.',
                'category' => 'History',
                'year' => 2014,
                'keywords' => 'history, anthropology, civilization, humanity',
            ],
            [
                'title' => 'A Brief History of Time',
                'author' => 'Stephen Hawking',
                'description' => 'Cosmology for the general reader: black holes, big bang, and the nature of time.',
                'category' => 'Science',
                'year' => 1988,
                'keywords' => 'physics, cosmology, black holes, universe, time',
            ],
            [
                'title' => 'Linear Algebra Done Right',
                'author' => 'Sheldon Axler',
                'description' => 'Undergraduate linear algebra without determinants in the main development.',
                'category' => 'Mathematics',
                'year' => 2015,
                'keywords' => 'linear algebra, vector spaces, eigenvalues, textbook',
            ],
            [
                'title' => 'The Art of Computer Programming, Vol. 1',
                'author' => 'Donald E. Knuth',
                'description' => 'Fundamental algorithms and mathematical preliminaries for programming.',
                'category' => 'Computer Science',
                'year' => 1997,
                'keywords' => 'algorithms, programming, mathematics, classic',
            ],
            [
                'title' => 'Oxford English Dictionary Concise',
                'author' => 'Oxford University Press',
                'description' => 'Concise dictionary of current English usage and etymology.',
                'category' => 'Reference',
                'year' => 2011,
                'keywords' => 'dictionary, english, reference, language',
            ],
        ];

        foreach ($books as $row) {
            Book::query()->updateOrCreate(
                ['title' => $row['title'], 'author' => $row['author']],
                $row
            );
        }
    }
}

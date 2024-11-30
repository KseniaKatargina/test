-- Создание таблицы для записей
CREATE TABLE posts (
                       id SERIAL PRIMARY KEY,
                       userId INTEGER NOT NULL,
                       title TEXT NOT NULL,
                       body TEXT NOT NULL
);

-- Создание таблицы для комментариев
CREATE TABLE comments (
                          id SERIAL PRIMARY KEY,
                          postId INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
                          name TEXT NOT NULL,
                          email TEXT NOT NULL,
                          body TEXT NOT NULL
);

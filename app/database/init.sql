CREATE TABLE app__user
(
    id   varchar(4)  NOT NULL,
    nick varchar(42) NOT NULL,
    pass varchar(80),

    CONSTRAINT `app__user_pk` PRIMARY KEY (id)
)

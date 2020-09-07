CREATE TABLE th__challenge
(
    id               varchar(4)   NOT NULL,
    code             varchar(8)   NULL,
    title            varchar(72)  NOT NULL,
    description      text         NOT NULL,
    key_type         varchar(92)  NOT NULL,
    key_type_options text         NOT NULL,
    on_submit        varchar(4)   NULL,
    correct_answer   varchar(256) NOT NULL,

    CONSTRAINT `th__challenge_pk` PRIMARY KEY (id),
    CONSTRAINT `th__challenge_script_fk` FOREIGN KEY
        (on_submit) REFERENCES exe__action (id)
);

CREATE TABLE th__narrative
(
    id                     varchar(6)  NOT NULL,
    title                  varchar(72) NOT NULL,
    content                text        NOT NULL,
    following_challenge_id varchar(4)  NULL,

    CONSTRAINT `th__narrative_pk` PRIMARY KEY (id),
    CONSTRAINT `th__narrative_following_challenge_fk` FOREIGN KEY
        (following_challenge_id) REFERENCES th__challenge (id)
        ON DELETE SET NULL ON UPDATE CASCADE

);

CREATE TABLE th__treasure_map
(
    id       varchar(24)  NOT NULL,
    name     varchar(72)  NOT NULL,
    filename varchar(120) NOT NULL,

    tiling_x int          NOT NULL DEFAULT 1,
    tiling_y int          NOT NULL DEFAULT 1,

    CONSTRAINT `th__treasure_map_pk` PRIMARY KEY (id)
);

CREATE TABLE th__notebook
(
    id              varchar(6)   NOT NULL,
    user_id         varchar(4)   NULL,
    active_page     INT UNSIGNED NULL,
    first_opened_at TIMESTAMP    NOT NULL,

    CONSTRAINT `th__notebook_pk` PRIMARY KEY (id),
    CONSTRAINT `th_notebook_to_user_fk` FOREIGN KEY
        (user_id) REFERENCES app__user (id)
);

CREATE TABLE th__notebook_page
(
    id          varchar(6)   NOT NULL,
    notebook_id varchar(6)   NOT NULL,
    page_number INT UNSIGNED NOT NULL,
    type        VARCHAR(24)  NOT NULL,
    params      TEXT         NOT NULL,

    CONSTRAINT `th__notebook_page_pk` PRIMARY KEY (id),
    CONSTRAINT `th_notebook_page_to_notebook_fk` FOREIGN KEY
        (notebook_id) REFERENCES th__notebook (id),
    CONSTRAINT `th__notebook_page_unique` UNIQUE (notebook_id, page_number)
);

CREATE TABLE th__clue_revelation
(
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    notebook_page_id VARCHAR(6)   NOT NULL,
    expires_on       DATETIME     NULL,
    clue_type        VARCHAR(24)  NOT NULL,
    clue_args        TEXT         NOT NULL,
    date_created     DATETIME     NOT NULL,

    CONSTRAINT `th__clue_revelation_pk` PRIMARY KEY (id),
    CONSTRAINT `th__clue_revelation_to_notebook_page_fk` FOREIGN KEY
        (notebook_page_id) REFERENCES th__notebook_page (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE th__input_ban
(
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    notebook_page_id varchar(6)   NOT NULL,
    active_until     DATETIME     NOT NULL,

    CONSTRAINT `th__input_ban_pk` PRIMARY KEY (id),
    CONSTRAINT `th__input_ban_to_notebook_page_fk` FOREIGN KEY
        (notebook_page_id) REFERENCES th__notebook_page (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE th__challenge
(
    id          varchar(4)  NOT NULL,
    title       varchar(72) NOT NULL,
    description text        NOT NULL,
    key_type    varchar(24) NOT NULL,

    CONSTRAINT `th__challenge_pk` PRIMARY KEY (id)
);
CREATE TABLE th__action
(
    id           varchar(4)  NOT NULL,
    challenge_id varchar(4)  NOT NULL,
    sequence     int         NOT NULL,
    type         varchar(24) NOT NULL,
    params       text        NOT NULL,

    CONSTRAINT `th__action_pk` PRIMARY KEY (id),
    CONSTRAINT `th__action_sequence_unique` UNIQUE (id, sequence),
    CONSTRAINT `th__action_to_challenge_fk` FOREIGN KEY
        (challenge_id) REFERENCES th__challenge (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE th__condition
(
    id     varchar(6)  NOT NULL,
    type   varchar(24) NOT NULL,
    params text        NOT NULL,

    CONSTRAINT `th__condition_pk` PRIMARY KEY (id)
);

CREATE TABLE th__action_has_condition
(
    action_id    varchar(6) NOT NULL,
    condition_id varchar(6) NOT NULL,

    CONSTRAINT `th__action_has_condition_pk` PRIMARY KEY (action_id, condition_id),
    CONSTRAINT `th__action_has_condition_action_fk` FOREIGN KEY
        (action_id) REFERENCES th__action (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `th__action_has_condition_condition_fk` FOREIGN KEY
        (condition_id) REFERENCES th__condition (id) ON DELETE CASCADE ON UPDATE CASCADE

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

    CONSTRAINT `th__notebook_page_pk` PRIMARY KEY (id),
    CONSTRAINT `th_notebook_page_to_notebook_fk` FOREIGN KEY
        (notebook_id) REFERENCES th__notebook (id),
    CONSTRAINT `th__notebook_page_unique` UNIQUE (notebook_id, page_number)
);

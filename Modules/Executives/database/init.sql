CREATE TABLE exe__script
(
    id     varchar(4)  NOT NULL,
    method varchar(24) NOT NULL,

    CONSTRAINT `exe__script_pk` PRIMARY KEY (id)
);

CREATE TABLE exe__action
(
    id        varchar(4)  NOT NULL,
    script_id varchar(4)  NOT NULL,
    sequence  int         NOT NULL,
    type      varchar(48) NOT NULL,
    params    text        NOT NULL,

    CONSTRAINT `exe__action_pk` PRIMARY KEY (id),
    CONSTRAINT `exe__action_sequence_unique` UNIQUE (script_id, sequence),
    CONSTRAINT `exe__action_to_script_fk` FOREIGN KEY
        (script_id) REFERENCES exe__script (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE exe__condition
(
    id     varchar(6)  NOT NULL,
    type   varchar(48) NOT NULL,
    params text        NOT NULL,

    CONSTRAINT `th__condition_pk` PRIMARY KEY (id)
);

CREATE TABLE exe__action_has_condition
(
    action_id    varchar(6) NOT NULL,
    condition_id varchar(6) NOT NULL,

    CONSTRAINT `th__action_has_condition_pk` PRIMARY KEY (action_id, condition_id),
    CONSTRAINT `th__action_has_condition_action_fk` FOREIGN KEY
        (action_id) REFERENCES exe__action (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `th__action_has_condition_condition_fk` FOREIGN KEY
        (condition_id) REFERENCES exe__condition (id) ON DELETE CASCADE ON UPDATE CASCADE

);

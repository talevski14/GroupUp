create table users
(
    id        bigint unsigned auto_increment,
    name      varchar(255)                                       not null,
    username  varchar(255)                                       not null,
    password  varchar(255)                                       not null,
    profpic   varchar(255) default '/images/account/default.jpg' null,
    email     varchar(255)                                       not null,
    societies text                                               null,
    active    tinyint(1)   default 1                             null,
    constraint id
        unique (id)
);


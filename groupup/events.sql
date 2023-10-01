create table events
(
    id            bigint unsigned auto_increment,
    name          varchar(255)                                                           not null,
    society       bigint                                                                 not null,
    attending     text collate utf8mb4_bin                                               null,
    date_and_time datetime                                                               not null,
    creator       bigint                                                                 not null,
    description   varchar(255)                                                           null,
    discussion    text                                                                   null,
    created_on    datetime                                                               null,
    lat           varchar(255)                                                           null,
    lon           varchar(255)                                                           null,
    location      varchar(255) default 'For the details please look at the map below...' null,
    passed        tinyint(1)   default 0                                                 null,
    constraint id
        unique (id)
);


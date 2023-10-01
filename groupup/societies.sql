create table societies
(
    id          bigint unsigned auto_increment,
    members     text                                              null,
    events      text                                              null,
    name        varchar(255)                                      not null,
    banner      varchar(255) default '/images/society/banner.jpg' not null,
    description text                                              null,
    constraint id
        unique (id)
);


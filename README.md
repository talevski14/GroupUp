# GroupUp

Welcome to GroupUp, the go-to social media platform for crafting memorable moments with friends. Seamlessly organize events, expand your social circle, and foster lasting connections. With secure user authentication, effortlessly manage your account while delving into a world of possibilities.

Create and lead your own communities, joining or leaving groups at your convenience. Craft events effortlessly, specifying every detail. Get quick event responses and engage in event-centric discussions, adding an extra layer of excitement. Stay weather-ready with real-time updates and navigate event locations effortlessly using integrated Microsoft Bing Maps. With an intuitive design, GroupUp ensures smooth interaction, letting you focus on what truly matters—connecting with your clique and curating unforgettable experiences.

Elevate your social life with GroupUp, where seamless event planning meets vibrant social networking. Join now and embark on a journey of shared moments and endless fun.

## Technologies
Behind the scenes, GroupUp is powered by state-of-the-art technologies. PHP Slim drives our robust backend, while Twig templates create a dynamic and intuitive frontend. Your data is securely handled through MariaDB, guaranteeing user privacy. Through the prowess of Guzzle, GroupUp seamlessly integrates with [OpenMeteo](https://open-meteo.com/) for real-time weather updates. Plus, with [Bing Maps](https://www.microsoft.com/en-us/maps/bing-maps/choose-your-bing-maps-api) integration, navigating to event locations is a breeze.

## Installation

Since the app is built in a docker container, you need to take a few steps before running it:

- Install Docker on your computer. You can find the installation instructions for your operating system here: https://docs.docker.com/get-started/

- Install Docker Compose. You can find the installation instructions for your operating system here: https://docs.docker.com/compose/install/

- Clone the GroupUp repository from GitLab:
```bash
git clone https://practice.grabit.io/zoran.trpcheski/groupup.git
```

- Change the directory into the GroupUp directory:
```bash
cd groupup
```

- Run the following command to build the container:
```bash
docker compose up -d
```

- Check the ID of the php container:
```bash
docker ps
```

- Open the php container terminal where instead of the PHP_CONTAINER_ID you will write the container ID that you got from the previous command:
```bash
docker exec -it PHP_CONTAINER_ID sh
```

- Run the following command inside the container:
```bash
composer install
```

- Now, you need to construct the data tables used in the app. Run the SQL migration scripts found in the groupup folder in the repository.

- You are now set to enjoy GroupUp.

*if you are on a MAC computer, you probably need to change the database image inside the docker-compose.yml from mariadb:latest to mysql:latest.

## Credits
This project was developed by Nikola Talevski while being an intern at GrabIT under the mentorship of Zoran Trpcheski.
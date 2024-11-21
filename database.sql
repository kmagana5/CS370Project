
CREATE TABLE Category (
                          category_id INT NOT NULL AUTO_INCREMENT,
                          description VARCHAR(255) NOT NULL,
                          PRIMARY KEY (category_id)
);

CREATE TABLE Source (
                        source_id INT NOT NULL AUTO_INCREMENT,
                        url VARCHAR(255) NOT NULL,
                        organization VARCHAR(255) NOT NULL,
                        PRIMARY KEY(source_id)
);

CREATE TABLE Story (
                       story_id INT NOT NULL AUTO_INCREMENT,
                       headline VARCHAR(255) NOT NULL,
                       views INT NOT NULL,
                       publish_date DATETIME NOT NULL,
                       category_id INT,
                       source_id INT,
                       PRIMARY KEY (story_id),
                       CONSTRAINT FK_Story_Category FOREIGN KEY (category_id) REFERENCES Category(category_id),
                       CONSTRAINT FK_Story_Source FOREIGN KEY (source_id) REFERENCES Source(source_id)
);

CREATE TABLE Author (
                        author_id INT NOT NULL AUTO_INCREMENT,
                        first_name VARCHAR(255) NOT NULL,
                        last_name VARCHAR(255) NOT NULL,
                        alma_mater VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL,
                        PRIMARY KEY (author_id)
);

CREATE TABLE Written (
                         story_id INT NOT NULL,
                         author_id INT NOT NULL,
                         PRIMARY KEY (story_id, author_id),
                         CONSTRAINT FK_Written_Story FOREIGN KEY (story_id) REFERENCES Story(story_id),
                         CONSTRAINT FK_Written_Author FOREIGN KEY (author_id) REFERENCES Author(author_id)
);

CREATE TABLE EditorNotes (
                             note_id INT NOT NULL AUTO_INCREMENT,
                             story_id INT NOT NULL,
                             author_id INT NOT NULL,
                             time_added DATETIME NOT NULL,
                             PRIMARY KEY (note_id, story_id),
                             CONSTRAINT FK_EditorNotes_Story FOREIGN KEY (story_id) REFERENCES Story(story_id),
                             CONSTRAINT FK_EditorNotes_Author FOREIGN KEY (author_id) REFERENCES Author(author_id)
);

CREATE TABLE Image (
                       image_id INT NOT NULL AUTO_INCREMENT,
                       story_id INT NOT NULL,
                       image_file VARCHAR(255) NOT NULL,
                       alt_text VARCHAR(255) NOT NULL,
                       date_uploaded VARCHAR(255) NOT NULL,
                       PRIMARY KEY (image_id, story_id),
                       CONSTRAINT FK_Image_Story FOREIGN KEY (story_id) REFERENCES Story(story_id)
);

CREATE TABLE Analytics (
                           story_id INT NOT NULL,
                           views INT NOT NULL,
                           likes INT NOT NULL,
                           shares INT NOT NULL,
                           time_reading_in_minutes INT NOT NULL,
                           PRIMARY KEY (story_id),
                           CONSTRAINT FK_Analytics_Story FOREIGN KEY (story_id) REFERENCES Story(story_id)
);



CREATE TABLE Advertiser (
                            advertiser_id INT NOT NULL AUTO_INCREMENT,
                            company_name VARCHAR(255) NOT NULL,
                            contact_person VARCHAR(255) NOT NULL,
                            business_email VARCHAR(255) NOT NULL,
                            category_id INT,
                            PRIMARY KEY (advertiser_id),
                            CONSTRAINT FK_Advertiser_Category FOREIGN KEY (category_id) REFERENCES Category(category_id)
);

CREATE TABLE Subscription (
                              subscription_tier INT NOT NULL AUTO_INCREMENT,
                              price DECIMAL(6,2) NOT NULL,
                              PRIMARY KEY (subscription_tier)
);

CREATE TABLE Card (
                      card_id INT NOT NULL AUTO_INCREMENT,
                      card_num INT NOT NULL,
                      security_code INT NOT NULL,
                      expires_on VARCHAR(255) NOT NULL,
                      zip INT NOT NULL,
                      PRIMARY KEY (card_id)
);

CREATE TABLE User (
                      user_id INT NOT NULL AUTO_INCREMENT,
                      first_name VARCHAR(255) NOT NULL,
                      last_name VARCHAR(255) NOT NULL,
                      display_name VARCHAR(255) NOT NULL,
                      email VARCHAR(255) NOT NULL,
                      card_id INT,
                      subscription_status INT,
                      PRIMARY KEY (user_id),
                      CONSTRAINT FK_User_Card FOREIGN KEY (card_id) REFERENCES Card(card_id),
                      CONSTRAINT FK_User_Subscription FOREIGN KEY (subscription_status) REFERENCES Subscription(subscription_tier)
);

CREATE TABLE Comments (
                          user_id INT NOT NULL,
                          story_id INT NOT NULL,
                          time_posted DATETIME NOT NULL,
                          reply_count INT NOT NULL,
                          PRIMARY KEY (user_id, story_id, time_posted),
                          CONSTRAINT FK_Comments_User FOREIGN KEY (user_id) REFERENCES User(user_id),
                          CONSTRAINT FK_Comments_Story FOREIGN KEY (story_id) REFERENCES Story(story_id)
);
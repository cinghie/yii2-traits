Yii2 Traits
------------

Yii2 Traits is a library with most used Traits

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require cinghie/yii2-traits "*"
```

or add

```
"cinghie/yii2-traits": "*"
```

## Configuration

Add in your configuration file the translations

```	
'components' => [ 

    // Internationalization
    'i18n' => [
        'translations' => [
            'traits' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vendor/cinghie/yii2-traits/messages',
            ],
        ],
    ],
                   	
]	
```

## Usage Example

To include a Trait in your Model:

```	
class YourModel extends ActiveRecord 
{
    use \cinghie\traits\CreatedTrait;
}
```

Merge rules() and attributeLabels():

```	
public function rules()
{
    return array_merge(
        CreatedTrait::rules(), 
        ModifiedTrait::rules(), 
        StateTrait::rules(), 
        UserTrait::rules(), 
        [your_rules]
    );
}    

public function attributeLabels()
{
    return array_merge(
        CreatedTrait::attributeLabels(), 
        ModifiedTrait::attributeLabels(), 
        StateTrait::attributeLabels(), 
        UserTrait::attributeLabels(), 
        [your_attributeLabels]
    );
}   
```

All functions implemented in the Traits can be called like as any function of the model

```
if( $model->isCurrentUserCreator() ) { 
    // your code
}    
```

## Traits

### CreatedTrait

Add in your model: 

    - created: Datetime of created Model
    - created_by: Integer userid of created Model 
    - function getCreatedBy(): Relation with User Model
    - function isCurrentUserCreator(): Check if current user is the created_by
    - function isUserCreator($user_id): Check if user_id params is the created_by
    
### LanguageTrait

Add in your model: 

    - language: String language
    - function getLanguagesSelect2(): Return an array with languages allowed

### ModifiedTrait

Add in your model: 

    - modified: Datetime of modified Model
    - modified_by: Integer userid of modified Model 
    - function getModifiedBy(): Relation with User Model
    - function isCurrentUserModifier(): Check if current user is the modified_by
    - function isUserModifier($user_id): Check if user_id params is the modified_by    

### NameAliasTrait

Add in your model: 

    - alias: string alias
    - name: string name  
    - function generateAlias($name): Generate URL alias by name
    
### StateTrait

Add in your model: 

    - state: Integer for state active (1) o inactive (0)
    - function active(): Active model state (Set 1)
    - function inactive():  Inactive model state (Set 0)
    
### TitleAliasTrait

Add in your model: 

    - alias: string alias
    - title: string title  
    - function generateAlias($title): Generate URL alias by title
    
### UserTrait

Add in your model: 

    - user_id: Integer userid of User Model
    - function getUser(): Relation with User Model   
    
### UserHelperTrait

Add in your model: 

    - function getRoles(): Return an array with the User's Roles 
    - function getUserIdByEmail($email): Get the user_id By user email 
    - function getUsersSelect2(): Return array with all Users (not blocked or not unconfirmed)
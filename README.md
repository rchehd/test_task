## Implementstion of solution of test task.

### Note: Cron schedule can be configured by "Ultimate cron" module, there we can set cron job schedule as "Crontab" with rules "0 18,8 * * *"

Initial info:
- We have entity with type BigEntity
- Date of creation field - 'field_time_created'. Format - timestamp.
- Unique id of the entity could be required with $entity->id() method.
- Id of the user that created entity could be required with $entity->uid() method.
- Admin uid is 1
- Entity has bool field 'field_in_progress' which could be used in the process (but it's not mandatory). Defaul field value is FALSE

Task:
In hook_cron implement logic to get entities of type BigEntity which:
- were created for the last day
- have no TRUE in field called 'field_processed'

Implement logic to perform operations described in section 'Required operations'.
Assume that cron is pre-configured to be executed each 5 minutes.
Сonsider that the number of entities may be too big to process in sigle query / hook cron run.

Required operations.
- Check value of the field called 'field_location'.
- If value equals 'outside' perform the following operations:
1) Add message to the log 'processed entity' + entity id.
2) Send email to user (default drupal method, The subject and body of the email: "test").
3) Set TRUE to 'field_processed' field of entity.

- If value equals 'inside' perform the following operations:
1) Add message to the log 'processed entity' + entity id.
2) Add message to the log 'this entity is outside entity'.
2) Send email to user (default drupal method, The subject and body of the email: "test").
3) Send email to admin (default drupal method, The subject of the email: "test", body: "admin").
4) Set TRUE to 'field_processed' field of entity.

Additional requirements:
The site is used only by office employees during working hours from 8 to 17.
The site should not tripping during business hours.
Site should not get into Maintenance mode even outside business hours.
Priorities during implementation: Drupal way, Code performance and sustainability, OOP Principles.
Code should be extendable if additional operations added to Required operations.
Avoid code duplication where possible/
Be ready to explain chosen approach.
Please use Drupal 9 or 10 API
There is no need to add code with declaration of BigEntity entity, you could just assume it already exists in the system



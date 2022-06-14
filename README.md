PHP Laravel Code Challenge
==========================
We expect you to build a small Laravel application.
The application does not need a frontend; we want it to be an API-only project.

Below you will find the models we want you to work with and details about the endpoints we
want you to provide.

## Models
### Users Model
- id
- username
- name
- created_at
- updated_at
### Posts Model
- id
- title
- slug
- content
- is_published
- created_at
- updated_at
### Comments Model
- id
- content
- is_published
- created_at
- updated_at

## Relationship Requirements
- A user can post a post.
- A user can post many posts.
- A post can have many comments.
- A user can comment on many posts.
> You can freely modify the models above to meet the relationship requirements.

## API Endpoints Requirements
- The API endpoints must follow, as close as possible, the JSON API specifications.
- Errors must be handled and returned by the API, so whoever is consuming it will not see wild 500 exceptions.
- Public endpoints can be accessed by anyone consuming the API.
- Protected endpoints can only be accessed by an authenticated user.
- Both first and third-party clients will consume the API.

### Posts
#### List Posts Public:
- Public.
- Paginated.
- Only published posts.
- Should include the last 5 comments.
- Should include comment's total count.
#### List Posts Protected:
- Protected.
- Paginated.
#### View Post Public:
- Public.
- Only published posts.
#### View Post Protected:
- Protected.
#### Create Post:
- Protected.
#### Edit Post:
- Protected.
- Only the post owner can edit it.
#### Delete Post:
- Protected.
- Only the post owner can delete it.

### Comments
#### List Comments for a given Post Public:
- Public.
- Paginated.
- Only published comments.
#### List Comments for a given Post Protected:
- Protected.
- Paginated.
#### List Comments for a given User Public:
- Public.
- Paginated.
- Only published comments.
#### List Comments for a given User Protected:
- Protected.
- Paginated.
#### Create Comment:
- Protected
#### Edit Comment:
- Protected.
- Only the comment owner can edit it.
#### Delete Comment:
- Protected.
- Only the comment owner can delete it.

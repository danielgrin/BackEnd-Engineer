# Readme - Backend Test

## Available endpoints

#### Properties

__GET /api/properties/?[filters]__

Filterable by country/state/suburb as ?country=country&state=state&suburb=suburb

--

#### Property by Id

__GET /api/properties/{property_id}__

--

#### Property analytics

__GET /api/properties/{property_id}/analytics__

--

#### Create or update a property analytic

__POST/PUT /api/properties/{property_id}/analytics__

POST/PUT analytic_id and a value

--

#### Summary of analytics for all analytic types *

__GET /api/analytic_types/analytics/summary/?[filters]__

Summary of all analytics by analytic type as measured on properties filterable by country/state/suburb as ?country=country&state=state&suburb=suburb

\* See note below on routes.

--

#### Summary of analytics by type

__GET /api/analytic_types/{analytic_type_id}/analytics/summary/?[filters]__

Summary of analytics for a single analytic type, as measured for properties filterable by country/state/suburb as ?country=country&state=state&suburb=suburb

--

NB: As mentioned below this is a literal and niave implementation of the requirements, so as far as complete API goes, many routes are missing.


### Query filters and Collisions
Filtering of properties by suburb/state/country is available using URL queries. As far as API design goes, I'd suggest our main endpoints (as concrete nouns / resources) should be _properties_ and _analytics_. Location fields can be thought of as attributes, so treating them as filters seems appropriate here. In my opinion, good API endpoints should only deal with concrete things and actions that can be done on them. Attributes are optional.

If we were working with global property data, we will almost certainly find collisions with the suburb attribute, and in theory the state attribute also. We should enforce the use of all three criteria when requesting filtered properties, i.e a request for properties in a suburb must include state and country.

We could enforce this using multiple cascading routes however our endpoints are likely to become convoluted, verbose, and the API liable to break backward compatibility once we start adding more filtering options, and conceptually speaking, we are breaking our nouns-as-endpoints rule-of-thumb. Rather, we can enforce this filtering requirement with validation messages in our responses (see notes below on JSON Responses).


---

## NOTES:

### Test Time
This test ended up taking significantly more time than anticipated. Time to think through the problem domain to identify edge-cases and consider different approaches to the business requirements, time setup the development environment, to set up migrations and massage the data for import (i.e cleaning up the spreadsheet for auto-seeding...), to setup architecture and routes, to test and debug... during the normal course of development all of these can expose unforseen design constraints, gotchas and technology issues, which can stretch what otherwise might seem a simple task.

Taking a good few hours to do each task properly, and because the test is somewhat open ended regarding features that would normally be used in a production app (eg. authenticaion, logging, analytics, validation etc.), time to complete can increase significantly. Consequently, a number of tasks that would usually be done to bring the application up to production standard have not been completed.

In my opinion, as the Laravel documentation is very good indeed, an experienced developer could easily learn it on the job. This means that many tasks only require grunt work to complete and may not offer much insight into skillset or experience.

In my opinion, a measure of deeper knowledge might be better made by solving the core business problem, rather than focusing on the potentially step-by-step process of setting up a basic CRUD system using Laravel.

Having said that, this is just some (hopefully not unwarrented) feedback to describe why it took me longer than expected. Otherwise, I think it's a pretty good test. 

I have listed some of the items not completed below.

### Structure
To be honest, I find it difficult to make confident decisions around architecture without having more information about how the API is to be used, data consumed, and at a higher level, what other features and business considerations are required. As there are numerous structural and architectural options, more information would allow better informed decisions around balancing performance against simplicity / efficiency against code coherence. This implementation therefor takes a more or less literal and naive approach to the problem.

### Routes
There are of course numerous ways to structure our endpoints, but for a final design, we would defer to what makes the most sense from a business perspective.

For example, the current route for fetching summaries of analytics, _url.com/api/analytic_types/analytics/summary_ is likely incongruous with what the business is actually asking for. It's perhaps more likely that the business will always want summaries of analytics in the context of properties, in which case a route like _url.com/api/properties/analytics/summary_ might make more sense.

On the other hand, perhaps we want to keep our resource concepts to as few as possible by hiding our analytic_types model completely and exposing everything through _/analytics/_

* _/analytics/summaryByType_
* _/analytics/addType_
...

...and to consider another example mentioned earlier, perhaps the business wants to be able to represent results in a more friendly way (in a website example, say), with something like:

* _/properties/australia/nsw/ryde/_

... instead of 

* _/api/properties/?country=australia&state=nsw&suburb=ryde_

Again, we can approach endpoint design in numerous ways and Laravel is flexible enough to give use full control here. We might even decide to provide multiple endpoints to access the same data with cascading routes or (more likely) custom server-side rewrites, to serve both technical and non-technical needs.

The point is, our final presentation should make the most sense for the business and we should avoid putting unnecessary technical constraints on the end user for the sake of architectural purity. Conceptually correct is not always the same as intuitively correct, or indeed practically useful.

Having said that, here I have used _/analytic_types/analytics/summary_ as it better reflects what is going on behind the scenes. In this implementation we are summarising analytics by type, and sub-filtering these by the properties that they apply to (as discussed further below), so in my opinion, this endpoint is more "correct" from an architectural perspective, at least in the abstract. It also starts to make more sense once we add GET/PUT/POST/DELETE to _/analytic_types/_, as well as allowing us to summarise each one individually.


### Solution
As far as I understand it, the most interesting question in this exercise is finding a good way to summarise analytics for a given set of properties. As usual there are a number of approaches available here, each with advantages and trade-offs. Most important decisions in software development are about identifying acceptable tradeoffs, -balancing business needs with optimal code-craft, simplicity with performance. Clarity for power.

One approach here would be to do as much work as possible using the framework, which gives the advantage of cleaner and more standardised code and architecture. With a good framework (like Laravel) we can often deliver business requirements more rapidly this way. Here, we might use the ORM to fetch each analytic type one by one and summarise the values using the aggregate and collection functions built into Laravel. 

The trade-off here is that the overhead of the framework sits between us and the language and this may not offer the best performance at scale. In many applications, this won't be a problem, however once an application grows large enough or utilises large amounts of data, we may find that it does not perform adequately with real-world use. 

Another option might be to fetch all analytics in one go, then dice, slice and summarise them using standard PHP. Here we would be processing a large number of records in one go, and we might initially suspect this to be more efficient. For some tasks this may be the case, but the trade-off is needing more custom code, and we will necessarily use more memory. In my experience working with geographical data, these datasets can be very large. I have seen PHP flounder numerous times when crunching datasets constisting of GIS data covering an entire country, so I would be reluctant to use this as the first approach, without knowing more about the target data.

A third approach would be to get the database to do as much of the heavy lifting as possible with raw SQL statements, using the aggregate functions included with MySql, and/or by using a dedicated analytics_summaries view tables. As a rule of thumb, doing as much as possible at the DB level will generally be most efficient, however the trade-off here is that for most developers, complex SQL statements can take longer to write, can be more difficult to integrate (keeping good separation of concerns architecturally), and can become more difficult to maintain. However, if we are after pure performance, we can generally rely on this approach to deliver. In this instance, I would likely consider a view table to be a good solution.

For this exercise I have chosen to make use of the framework as much as possible, in this case using the ORM to fetch and summarise analytics by type, one by one, making no assumptions about target data. This is a slow and steady approach that should keep memory usage down and the server stable. In a real world situation, this implementation would only be the first step. Keeping in mind the maxim "make it work, then make it good", the next phase would be to profile the application at scale, with real-world data, so we know exactly where to focus when refactoring. With actual performance metrics to work against we can avoiding premature optimisation and manage up-front development costs by only optimising what will offer actual business value.

Perhaps it is cheaper for the business to throw more hardware at the problem, perhaps by building the app as quickly as possible and spinning up a cluster of instances to handle load rather than optimising code. In a startup situation, this is often the most practical way forward in the short term.

With an initial, testable prototype we could now plan a longer term development strategy with concrete metrics to consider and measure our work against.

So, in answer to the question of a good way to summarise analytics... as is very often the case, it depends.

## Caching and Preprocessing
It's unlikely that we are going to want to run the same processing code for each request, so at scale, some sort of caching strategy should be considered. This could be as simple as using the built in caching features in Laravel or setting up a dedicated caching layer as part of our server stack -perhaps as a distributed cluster of key-value databases (e.g Redis). We might also consider using a cloud based service such as IronCache or AWS ElasticCache so that scaling and regional distribution is handled for us.

Additionally, we would look into data preprocessing. As new data arrives, throwing it into a queue and spinning up a cluster of worker instances to crunch and store the results in a dedicated summaries table and/or in the cache. We might consider using server-less infrastructure such as AWS Lambda, to give us a theoretically infinite processes to handle data as it arrives. As another example, we could build stand-alone processing modules in a lower level language that supports concurrency (e.g GoLang), to increase processing efficiency even more.

Again, with a initial working application and knowledge about anticipated business needs, we could now decide on a strategy to suit the enterprise as a whole, over the full life-cycle of the application.

---

## Missing Functionality
As mentioned earlier, the following would need to be completed to make this application suitable for release, in no particular order:

**Analytic Types** - with many analytic types, perhaps requiring complex calculations, we would likely use a package of custom classes and libraries to manage them. Here I am simply using basic arrays and objects, along with built-in aggregate functions. Experience with complex GIS data suggests that a more robust solution to handle analytics might be needed.

**Controllers and REST functions** - we would usually set up a controller layer to handle all REST verbs. To keep boilerplate code down, I have opted here to do all the work using routes. For a small application this would be a reasonable way to avoid unnecessary overhead.

**JSON Responses** - without knowing anything about who will be consuming the data, it's difficult to make a decision on structuring responses. I normally prefer to send JSON output with meta data, however this might not always be appropriate. As an example, I would usually send something like the following (pseudo):

server_response (ok/error)
errors
    error_code
    error_messages (friendly)
pagination_info
    total_records
    current    ...
original_request (useful for generic interfaces or broadcasting)
data []

...in this instance, I am returning the data only, with empty placeholders when no records are found. For POSTS/PUTS, I am simply returning an ok/notok string. Again, the JSON output would usually be much more useful.

**Validation** - we would normally not allow mass-assignment on POSTS or PUTS but would properly process user input. We would also return meaningful validation errors to the client. Again, to avoid extra grunt-work I have kept things as simple as possible.

**Authentication** - we would normally have a user management / authentication system. Even for a public facing API we would have some sort of guest functionality, with tracking and logging. For now I have only included the built in throttling middleware -the very least one would need for an API.

**Tracking and User Analytics** - ...

**Caching** - as discussed earlier.

**Logging** - error logging, possibly using an external application or service handle aggregation and real-time error alerts. I'm a big fan of chat-ops here for system monitoring and management (e.g project, code delivery and infrastructure management through a Slack channel).

**Testing** - TDD is usually an ideal development approach, with clear advantages, however it does have downsides. Being able to develop against tests can make new features and refactoring super fast down stream, however it does take significantly more development time up-front to be done properly.

Personally I find TDD during prototyping / discovery phase to be an impediment to rapid development. My preferred approach is to prototype as rapidly as possible then, having something to design against, implement unit testing in a much more informed way. If one starts with the overhead of unit testing, it becomes more difficult to quickly experiment with different architectural patterns. 

Integration and functional testing arguably more important than unit testing at the enterprise level, and this too should be automated as part of a complete DevOps Strategy. For the purposes of this exercise I have not included any testing.

**API Documentation** - as this is an API, DockBlock commenting would be desirable to allow for automated documentation. Doxygen or a system like Swagger could be used to automatically generate meaningful documentation. For this exercise I have only commented the code flow.
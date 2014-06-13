<div class="section">
	<div class="title">
		Who, why?
	</div>
	<div class="content">
		<p>
			Me (capob).  I bring to you Deemit because it has the potential to be of use to society.  The system has a significant potential, but that potential is based on user contribution.  It is not my intention to fuel controversy.  I have designed the system to limit the influence of those seeking only controversy.  However, it is sometimes necessary to controvert established ideas to move on to better ideas.
		</p>
	</div>
</div>
<div class="section">
	<div class="title">
		Voting &amp; Significance
	</div>
	<div class="content">
		<p>
		Users may vote on comments, entities, and entity relations.  The vote adds or deducts from the significance of the item by a percentage of the voter's significance.  If an item's significance, as a result of voting, drops below zero, that item is deleted and the user creating the item is punished by a reduction in user significance.  If a user's signifance drops below zero, that user is disabled. 
		</p>
		<p>
		There are various ways a user can gain significance.  Most of these ways involve being an active memeber in the community by posting relations and entities.
		</p>
		<p>
		The significance meters represent a calculation involving deviation from the mean, in which a meter displaying half full would indicate no or very little deviation, and a meter showing more than half full would indicate more significance than the average for that type.
		</p>
	</div>
</div>

<div class="section">
	<div class="title">
		Entities
	</div>
	<div class="content">
		There are many <a href="/entity/types">types</a> of entities.  I may expand the types of entities if requested to do so.
	</div>
</div>

<div class="section">
	<div class="title">
		Entities Relations
	</div>
	<div class="content">
		Entity relations can have two factors:
		<div class="section">
			<div class="title" id="forFactor">
				Against/For Factor
			</div>
			<div class="content">
				This factor can be seen in a number of ways; here are some:
				<ul>
					<li>Person X is against Enemy Y</li>
					<li>Person X is for friend Y</li>
					<li>Person X is for employer Y by nature of being employed by employer Y</li>
					<li>Company X is for product Y by nature of producing product Y</li>
					<li>Person X is for moral Z</li>
				</ul>
				
				A "0" against/for factor indicates neutrality and is not factored in to compiled relations.
			</div>
		</div>
		
		<div class="section">
			<div class="title" id="controlFactor">
				Control/Influence Factor
			</div>
			<div class="content">
				This factor indicates how much control or influence on entity has over another.  This factor can be seen in a number of ways; here are some:
				<ul>
					<li>Parent X has large control over his child Y</li>
					<li>Employer X has moderate control over employee Y</li>
					<li>HOA X has complete control over HOA fees</li>
					<li>Organization X has moderate control over government Y</li>
				</ul>
				A "0" control/influence factor indicates neutrality and is not factored in to compiled relations.
			</div>
		</div>
		<p>
			The relater is the one with the control of the relatee, or the one who is for or against the relatee.
		</p>
		<div class="section">
			<div class="title" id="directRelations">
				Direct Relations
			</div>
			<div class="content">
				<p>
				Relations between two entities with no other entities in the relation.  These are user submitted relations and are subject to voting.
				</p>
			</div>
		</div>
		
		<div class="section">
			<div class="title" id="compiledRelations">
				Compiled/Indirect Relations
			</div>
			<div class="content">
				<img src="/public/img/about_relations.png">
				<p>
				At an interval of time, the Deemit system compiles the indirection relations between entities and forms "compiled relations".  A compiled relation is a sort of sum of all the indirect relations between two entities.  The value of the factors and the significance of a compiled relation depends, of course, on the quality and number of paths indirectly relating two entities.  The algorithms doing this are high database and cpu usage algorithms, so it will be interesting to see how much the server can handle.
				</p>
			</div>
		</div>
		
	</div>
</div>
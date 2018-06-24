

function cc_create_domain(domainId) {

	const domain = await colonyClient.getDomain.call({ domainId: domainId });
	colonyClient.addDomain.send({ parentSkillId: domain.localSkillId })

}

